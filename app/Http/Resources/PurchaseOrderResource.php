<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PurchaseOrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'order_number' => $this->order_number,
            'order_date' => $this->order_date,
            'expected_date' => $this->expected_date,
            'status' => $this->status,
            'subtotal' => $this->subtotal,
            'tax_amount' => $this->tax_amount,
            'discount_amount' => $this->discount_amount,
            'shipping_amount' => $this->shipping_amount,
            'total_amount' => $this->total_amount,
            'paid_amount' => $this->paid_amount,
            'balance_amount' => $this->balance_amount,
            'payment_status' => $this->payment_status,
            'payment_terms' => $this->payment_terms,
            'delivery_address' => $this->delivery_address,
            'notes' => $this->notes,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'supplier' => [
                'id' => $this->supplier->id,
                'name' => $this->supplier->name,
                'email' => $this->supplier->email,
                'phone' => $this->supplier->phone,
            ],
            'items' => $this->items->map(function ($item) {
                return [
                    'id' => $item->id,
                    'item' => [
                        'id' => $item->item->id,
                        'code' => $item->item->code,
                        'name' => $item->item->name,
                        'description' => $item->item->description,
                        'unit_of_measure' => $item->item->unit_of_measure,
                    ],
                    'warehouse' => [
                        'id' => $item->warehouse->id,
                        'name' => $item->warehouse->name,
                    ],
                    'description' => $item->description,
                    'quantity' => $item->quantity,
                    'received_quantity' => $item->received_quantity,
                    'unit_price' => $item->unit_price,
                    'discount_percentage' => $item->discount_percentage,
                    'discount_amount' => $item->discount_amount,
                    'tax_percentage' => $item->tax_percentage,
                    'tax_amount' => $item->tax_amount,
                    'subtotal' => $item->subtotal,
                    'total' => $item->total,
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
