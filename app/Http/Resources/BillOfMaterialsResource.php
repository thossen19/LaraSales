<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BillOfMaterialsResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'bom_number' => $this->bom_number,
            'version' => $this->version,
            'status' => $this->status,
            'standard_cost' => $this->standard_cost,
            'notes' => $this->notes,
            'is_default' => $this->is_default,
            'effective_date' => $this->effective_date,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'item' => [
                'id' => $this->item->id,
                'code' => $this->item->code,
                'name' => $this->item->name,
                'description' => $this->item->description,
            ],
            'bom_items' => $this->bomItems->map(function ($bomItem) {
                return [
                    'id' => $bomItem->id,
                    'component_item' => [
                        'id' => $bomItem->componentItem->id,
                        'code' => $bomItem->componentItem->code,
                        'name' => $bomItem->componentItem->name,
                        'description' => $bomItem->componentItem->description,
                        'unit_of_measure' => $bomItem->componentItem->unit_of_measure,
                    ],
                    'quantity' => $bomItem->quantity,
                    'unit_of_measure' => $bomItem->unit_of_measure,
                    'scrap_percentage' => $bomItem->scrap_percentage,
                    'effective_quantity' => $bomItem->effective_quantity,
                    'notes' => $bomItem->notes,
                    'sequence' => $bomItem->sequence,
                ];
            }),
            'created_by' => [
                'id' => $this->createdBy->id,
                'name' => $this->createdBy->name,
                'email' => $this->createdBy->email,
            ],
        ];
    }
}
