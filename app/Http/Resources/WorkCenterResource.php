<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WorkCenterResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'name' => $this->name,
            'description' => $this->description,
            'location' => $this->location,
            'hourly_rate' => $this->hourly_rate,
            'capacity_hours' => $this->capacity_hours,
            'efficiency_percentage' => $this->efficiency_percentage,
            'effective_capacity' => $this->effective_capacity,
            'is_active' => $this->is_active,
            'notes' => $this->notes,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
