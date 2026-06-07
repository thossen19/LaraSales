<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FixedAssetResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'asset_number' => $this->asset_number,
            'name' => $this->name,
            'description' => $this->description,
            'category' => $this->category,
            'location' => $this->location,
            'purchase_date' => $this->purchase_date,
            'purchase_cost' => $this->purchase_cost,
            'current_value' => $this->current_value,
            'depreciation_method' => $this->depreciation_method,
            'useful_life_years' => $this->useful_life_years,
            'salvage_value' => $this->salvage_value,
            'depreciation_start_date' => $this->depreciation_start_date,
            'accumulated_depreciation' => $this->accumulated_depreciation,
            'status' => $this->status,
            'responsible_person' => $this->responsible_person,
            'notes' => $this->notes,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'annual_depreciation' => $this->annual_depreciation,
            'monthly_depreciation' => $this->monthly_depreciation,
            'remaining_life' => $this->remaining_life,
            'depreciation_percentage' => $this->depreciation_percentage,
            'created_by' => [
                'id' => $this->createdBy->id,
                'name' => $this->createdBy->name,
                'email' => $this->createdBy->email,
            ],
        ];
    }
}
