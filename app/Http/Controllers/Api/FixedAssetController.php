<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\FixedAssetRequest;
use App\Http\Resources\FixedAssetResource;
use App\Models\DepreciationRecord;
use App\Models\FixedAsset;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class FixedAssetController extends Controller
{
    public function index(Request $request)
    {
        $query = FixedAsset::with(['depreciationRecords' => function ($query) {
            $query->latest()->take(1);
        }, 'createdBy'])
        ->where('company_id', $request->user()->company_id);

        if ($request->has('category')) {
            $query->where('category', $request->category);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('asset_number', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%");
            });
        }

        $assets = $query->orderBy('created_at', 'desc')->paginate(15);

        return FixedAssetResource::collection($assets);
    }

    public function store(FixedAssetRequest $request)
    {
        try {
            DB::beginTransaction();

            $asset = FixedAsset::create([
                'company_id' => $request->user()->company_id,
                'name' => $request->name,
                'description' => $request->description,
                'category' => $request->category,
                'location' => $request->location,
                'purchase_date' => $request->purchase_date,
                'purchase_cost' => $request->purchase_cost,
                'depreciation_method' => $request->depreciation_method,
                'useful_life_years' => $request->useful_life_years,
                'salvage_value' => $request->salvage_value ?? 0,
                'depreciation_start_date' => $request->depreciation_start_date ?? $request->purchase_date,
                'status' => $request->status ?? 'active',
                'responsible_person' => $request->responsible_person,
                'notes' => $request->notes,
                'created_by' => $request->user()->id,
            ]);

            DB::commit();

            return new FixedAssetResource($asset->load(['depreciationRecords', 'createdBy']));
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Failed to create fixed asset: ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show(Request $request, FixedAsset $asset)
    {
        if ($asset->company_id !== $request->user()->company_id) {
            return response()->json(['message' => 'Unauthorized'], Response::HTTP_FORBIDDEN);
        }

        return new FixedAssetResource($asset->load(['depreciationRecords.createdBy', 'createdBy']));
    }

    public function update(FixedAssetRequest $request, FixedAsset $asset)
    {
        if ($asset->company_id !== $request->user()->company_id) {
            return response()->json(['message' => 'Unauthorized'], Response::HTTP_FORBIDDEN);
        }

        try {
            DB::beginTransaction();

            $asset->update([
                'name' => $request->name,
                'description' => $request->description,
                'category' => $request->category,
                'location' => $request->location,
                'purchase_date' => $request->purchase_date,
                'purchase_cost' => $request->purchase_cost,
                'depreciation_method' => $request->depreciation_method,
                'useful_life_years' => $request->useful_life_years,
                'salvage_value' => $request->salvage_value ?? $asset->salvage_value,
                'depreciation_start_date' => $request->depreciation_start_date ?? $asset->depreciation_start_date,
                'status' => $request->status ?? $asset->status,
                'responsible_person' => $request->responsible_person,
                'notes' => $request->notes,
            ]);

            DB::commit();

            return new FixedAssetResource($asset->load(['depreciationRecords', 'createdBy']));
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Failed to update fixed asset: ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function depreciate(Request $request, FixedAsset $asset)
    {
        if ($asset->company_id !== $request->user()->company_id) {
            return response()->json(['message' => 'Unauthorized'], Response::HTTP_FORBIDDEN);
        }

        if ($asset->status !== 'active') {
            return response()->json(['message' => 'Can only depreciate active assets'], Response::HTTP_BAD_REQUEST);
        }

        $request->validate([
            'depreciation_date' => 'required|date',
            'period' => 'required|in:monthly,quarterly,annually',
            'notes' => 'nullable|string|max:500',
        ]);

        try {
            DB::beginTransaction();

            $depreciationAmount = $request->period === 'monthly' ? $asset->monthly_depreciation : $asset->annual_depreciation;
            
            if ($depreciationAmount <= 0) {
                return response()->json(['message' => 'No depreciation to record'], Response::HTTP_BAD_REQUEST);
            }

            $newAccumulatedDepreciation = $asset->accumulated_depreciation + $depreciationAmount;
            $maxDepreciation = $asset->purchase_cost - $asset->salvage_value;
            
            if ($newAccumulatedDepreciation >= $maxDepreciation) {
                $depreciationAmount = $maxDepreciation - $asset->accumulated_depreciation;
                $newAccumulatedDepreciation = $maxDepreciation;
                $asset->status = 'fully_depreciated';
            }

            $newBookValue = $asset->purchase_cost - $newAccumulatedDepreciation;

            DepreciationRecord::create([
                'fixed_asset_id' => $asset->id,
                'depreciation_date' => $request->depreciation_date,
                'depreciation_amount' => $depreciationAmount,
                'accumulated_depreciation' => $newAccumulatedDepreciation,
                'book_value' => $newBookValue,
                'period' => $request->period,
                'notes' => $request->notes,
                'created_by' => $request->user()->id,
            ]);

            $asset->update([
                'accumulated_depreciation' => $newAccumulatedDepreciation,
                'current_value' => $newBookValue,
            ]);

            DB::commit();

            return new FixedAssetResource($asset->load(['depreciationRecords.createdBy', 'createdBy']));
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Failed to record depreciation: ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function dispose(Request $request, FixedAsset $asset)
    {
        if ($asset->company_id !== $request->user()->company_id) {
            return response()->json(['message' => 'Unauthorized'], Response::HTTP_FORBIDDEN);
        }

        if ($asset->status !== 'active') {
            return response()->json(['message' => 'Can only dispose active assets'], Response::HTTP_BAD_REQUEST);
        }

        $request->validate([
            'disposal_date' => 'required|date',
            'disposal_value' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:500',
        ]);

        try {
            DB::beginTransaction();

            $gainLoss = $request->disposal_value - $asset->current_value;

            $asset->update([
                'status' => 'disposed',
                'notes' => ($asset->notes ?? '') . "\n\nDisposed on " . $request->disposal_date . " for " . $request->disposal_value . ". Gain/Loss: " . $gainLoss . ". " . ($request->notes ?? ''),
            ]);

            DB::commit();

            return new FixedAssetResource($asset->load(['depreciationRecords', 'createdBy']));
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Failed to dispose asset: ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function report(Request $request)
    {
        $query = FixedAsset::where('company_id', $request->user()->company_id);

        if ($request->has('category')) {
            $query->where('category', $request->category);
        }

        $assets = $query->with(['depreciationRecords' => function ($query) {
            $query->latest()->take(1);
        }])->get();

        $summary = [
            'total_assets' => $assets->count(),
            'total_purchase_cost' => $assets->sum('purchase_cost'),
            'total_accumulated_depreciation' => $assets->sum('accumulated_depreciation'),
            'total_current_value' => $assets->sum('current_value'),
            'active_assets' => $assets->where('status', 'active')->count(),
            'disposed_assets' => $assets->where('status', 'disposed')->count(),
            'fully_depreciated_assets' => $assets->where('status', 'fully_depreciated')->count(),
        ];

        $byCategory = $assets->groupBy('category')->map(function ($categoryAssets) {
            return [
                'count' => $categoryAssets->count(),
                'purchase_cost' => $categoryAssets->sum('purchase_cost'),
                'current_value' => $categoryAssets->sum('current_value'),
                'accumulated_depreciation' => $categoryAssets->sum('accumulated_depreciation'),
            ];
        });

        return response()->json([
            'summary' => $summary,
            'by_category' => $byCategory,
            'assets' => $assets,
        ]);
    }
}
