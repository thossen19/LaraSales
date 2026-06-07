<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'name' => $this->name,
            'description' => $this->description,
            'category' => $this->category,
            'unit_of_measure' => $this->unit_of_measure,
            'purchase_price' => $this->purchase_price,
            'sale_price' => $this->sale_price,
            'cost_price' => $this->cost_price,
            'weight' => $this->weight,
            'volume' => $this->volume,
            'barcode' => $this->barcode,
            'image' => $this->image ? url('storage/items/' . $this->image) : null,
            'reorder_level' => $this->reorder_level,
            'reorder_quantity' => $this->reorder_quantity,
            'is_active' => $this->is_active,
            'is_stock_item' => $this->is_stock_item,
            'is_service' => $this->is_service,
            'sales_account' => $this->sales_account,
            'purchase_account' => $this->purchase_account,
            'inventory_account' => $this->inventory_account,
            'cogs_account' => $this->cogs_account,
            'notes' => $this->notes,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'current_stock' => $this->when(isset($this->currentStock), $this->currentStock),
        ];
    }
}
