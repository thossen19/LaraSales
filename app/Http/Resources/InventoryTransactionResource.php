<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InventoryTransactionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'transaction_type' => $this->transaction_type,
            'reference_type' => $this->reference_type,
            'reference_id' => $this->reference_id,
            'quantity' => $this->quantity,
            'unit_cost' => $this->unit_cost,
            'total_cost' => $this->total_cost,
            'quantity_before' => $this->quantity_before,
            'quantity_after' => $this->quantity_after,
            'notes' => $this->notes,
            'created_at' => $this->created_at,
            'item' => [
                'id' => $this->item->id,
                'code' => $this->item->code,
                'name' => $this->item->name,
                'unit_of_measure' => $this->item->unit_of_measure,
            ],
            'warehouse' => [
                'id' => $this->warehouse->id,
                'name' => $this->warehouse->name,
            ],
        ];
    }
}
