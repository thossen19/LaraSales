<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\DimensionRequest;
use App\Http\Resources\DimensionResource;
use App\Models\Dimension;
use App\Models\DimensionAssignment;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class DimensionController extends Controller
{
    public function index(Request $request)
    {
        $query = Dimension::with(['parent', 'children'])
            ->where('company_id', $request->user()->company_id);

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $dimensions = $query->orderBy('type')->orderBy('code')->get();

        return DimensionResource::collection($dimensions);
    }

    public function store(DimensionRequest $request)
    {
        try {
            DB::beginTransaction();

            $dimension = Dimension::create([
                'company_id' => $request->user()->company_id,
                'name' => $request->name,
                'description' => $request->description,
                'type' => $request->type,
                'parent_id' => $request->parent_id,
                'manager' => $request->manager,
                'budget_code' => $request->budget_code,
                'budget_amount' => $request->budget_amount ?? 0,
                'is_active' => $request->is_active ?? true,
                'notes' => $request->notes,
            ]);

            DB::commit();

            return new DimensionResource($dimension->load(['parent', 'children']));
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Failed to create dimension: ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show(Request $request, Dimension $dimension)
    {
        if ($dimension->company_id !== $request->user()->company_id) {
            return response()->json(['message' => 'Unauthorized'], Response::HTTP_FORBIDDEN);
        }

        return new DimensionResource($dimension->load(['parent', 'children', 'dimensionAssignments']));
    }

    public function update(DimensionRequest $request, Dimension $dimension)
    {
        if ($dimension->company_id !== $request->user()->company_id) {
            return response()->json(['message' => 'Unauthorized'], Response::HTTP_FORBIDDEN);
        }

        try {
            DB::beginTransaction();

            $dimension->update([
                'name' => $request->name,
                'description' => $request->description,
                'type' => $request->type,
                'parent_id' => $request->parent_id,
                'manager' => $request->manager,
                'budget_code' => $request->budget_code,
                'budget_amount' => $request->budget_amount ?? $dimension->budget_amount,
                'is_active' => $request->is_active ?? $dimension->is_active,
                'notes' => $request->notes,
            ]);

            DB::commit();

            return new DimensionResource($dimension->load(['parent', 'children']));
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Failed to update dimension: ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy(Request $request, Dimension $dimension)
    {
        if ($dimension->company_id !== $request->user()->company_id) {
            return response()->json(['message' => 'Unauthorized'], Response::HTTP_FORBIDDEN);
        }

        // Check if dimension has children
        if ($dimension->children()->exists()) {
            return response()->json(['message' => 'Cannot delete dimension with child dimensions'], Response::HTTP_BAD_REQUEST);
        }

        // Check if dimension has assignments
        if ($dimension->dimensionAssignments()->exists()) {
            return response()->json(['message' => 'Cannot delete dimension with existing assignments'], Response::HTTP_BAD_REQUEST);
        }

        $dimension->delete();

        return response()->json(['message' => 'Dimension deleted successfully']);
    }

    public function assign(Request $request)
    {
        $request->validate([
            'reference_type' => 'required|in:customer,supplier,item,user,account',
            'reference_id' => 'required|integer',
            'dimension_id' => 'required|exists:dimensions,id',
            'effective_date' => 'required|date',
            'end_date' => 'nullable|date|after:effective_date',
            'percentage' => 'nullable|numeric|min:0|max:100',
            'notes' => 'nullable|string|max:500',
        ]);

        try {
            DB::beginTransaction();

            $assignment = DimensionAssignment::create([
                'company_id' => $request->user()->company_id,
                'reference_type' => $request->reference_type,
                'reference_id' => $request->reference_id,
                'dimension_id' => $request->dimension_id,
                'effective_date' => $request->effective_date,
                'end_date' => $request->end_date,
                'percentage' => $request->percentage ?? 100,
                'notes' => $request->notes,
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Dimension assignment created successfully',
                'assignment' => $assignment->load('dimension'),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Failed to create dimension assignment: ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function assignments(Request $request)
    {
        $query = DimensionAssignment::with(['dimension'])
            ->where('company_id', $request->user()->company_id);

        if ($request->has('reference_type')) {
            $query->where('reference_type', $request->reference_type);
        }

        if ($request->has('reference_id')) {
            $query->where('reference_id', $request->reference_id);
        }

        if ($request->has('dimension_id')) {
            $query->where('dimension_id', $request->dimension_id);
        }

        if ($request->has('current')) {
            $query->current();
        }

        $assignments = $query->orderBy('effective_date', 'desc')->paginate(20);

        return response()->json($assignments);
    }

    public function tree(Request $request)
    {
        $dimensions = Dimension::where('company_id', $request->user()->company_id)
            ->active()
            ->with(['children' => function ($query) {
                $query->active()->orderBy('name');
            }])
            ->whereNull('parent_id')
            ->orderBy('type')
            ->orderBy('name')
            ->get();

        return DimensionResource::collection($dimensions);
    }

    public function report(Request $request)
    {
        $query = Dimension::where('company_id', $request->user()->company_id);

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        $dimensions = $query->with(['dimensionAssignments'])->get();

        $summary = [
            'total_dimensions' => $dimensions->count(),
            'total_budget' => $dimensions->sum('budget_amount'),
            'total_actual' => $dimensions->sum('actual_amount'),
            'total_variance' => $dimensions->sum('variance'),
            'over_budget' => $dimensions->where('variance', '<', 0)->count(),
            'under_budget' => $dimensions->where('variance', '>', 0)->count(),
        ];

        $byType = $dimensions->groupBy('type')->map(function ($typeDimensions) {
            return [
                'count' => $typeDimensions->count(),
                'budget' => $typeDimensions->sum('budget_amount'),
                'actual' => $typeDimensions->sum('actual_amount'),
                'variance' => $typeDimensions->sum('variance'),
            ];
        });

        return response()->json([
            'summary' => $summary,
            'by_type' => $byType,
            'dimensions' => $dimensions,
        ]);
    }
}
