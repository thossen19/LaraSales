<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductionOrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'order_number' => $this->order_number,
            'order_date' => $this->order_date,
            'start_date' => $this->start_date,
            'finish_date' => $this->finish_date,
            'status' => $this->status,
            'quantity_planned' => $this->quantity_planned,
            'quantity_produced' => $this->quantity_produced,
            'quantity_scrapped' => $this->quantity_scrapped,
            'standard_cost' => $this->standard_cost,
            'actual_cost' => $this->actual_cost,
            'notes' => $this->notes,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'progress_percentage' => $this->progress_percentage,
            'remaining_quantity' => $this->remaining_quantity,
            'item' => [
                'id' => $this->item->id,
                'code' => $this->item->code,
                'name' => $this->item->name,
                'description' => $this->item->description,
                'unit_of_measure' => $this->item->unit_of_measure,
            ],
            'warehouse' => [
                'id' => $this->warehouse->id,
                'name' => $this->warehouse->name,
            ],
            'created_by' => [
                'id' => $this->createdBy->id,
                'name' => $this->createdBy->name,
                'email' => $this->createdBy->email,
            ],
        ];
    }
}
