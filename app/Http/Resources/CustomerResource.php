<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomerResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'mobile' => $this->mobile,
            'fax' => $this->fax,
            'address' => $this->address,
            'city' => $this->city,
            'country' => $this->country,
            'postal_code' => $this->postal_code,
            'tax_id' => $this->tax_id,
            'customer_type' => $this->customer_type,
            'credit_limit' => $this->credit_limit,
            'current_balance' => $this->current_balance,
            'payment_terms' => $this->payment_terms,
            'sales_account' => $this->sales_account,
            'receivable_account' => $this->receivable_account,
            'is_active' => $this->is_active,
            'notes' => $this->notes,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
