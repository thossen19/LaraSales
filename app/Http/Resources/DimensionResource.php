<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DimensionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'name' => $this->name,
            'description' => $this->description,
            'type' => $this->type,
            'parent_id' => $this->parent_id,
            'manager' => $this->manager,
            'budget_code' => $this->budget_code,
            'budget_amount' => $this->budget_amount,
            'actual_amount' => $this->actual_amount,
            'variance' => $this->variance,
            'variance_percentage' => $this->variance_percentage,
            'is_active' => $this->is_active,
            'notes' => $this->notes,
            'full_path' => $this->full_path,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'parent' => $this->when($this->parent, [
                'id' => $this->parent->id,
                'name' => $this->parent->name,
            ]),
            'children' => $this->when($this->children, $this->children->map(function ($child) {
                return [
                    'id' => $child->id,
                    'name' => $child->name,
                    'code' => $child->code,
                ];
            })),
        ];
    }
}
