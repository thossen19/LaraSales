<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AccountResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'name' => $this->name,
            'description' => $this->description,
            'account_type' => $this->account_type,
            'account_category' => $this->account_category,
            'parent_code' => $this->parent_code,
            'level' => $this->level,
            'opening_balance' => $this->opening_balance,
            'current_balance' => $this->current_balance,
            'is_active' => $this->is_active,
            'is_contra' => $this->is_contra,
            'notes' => $this->notes,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'parent' => $this->when($this->parent, [
                'id' => $this->parent->id,
                'code' => $this->parent->code,
                'name' => $this->parent->name,
            ]),
            'children' => $this->when($this->children, $this->children->map(function ($child) {
                return [
                    'id' => $child->id,
                    'code' => $child->code,
                    'name' => $child->name,
                    'current_balance' => $child->current_balance,
                ];
            })),
        ];
    }
}
