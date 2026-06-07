<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class JournalEntryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'entry_number' => $this->entry_number,
            'entry_date' => $this->entry_date,
            'reference_type' => $this->reference_type,
            'reference_id' => $this->reference_id,
            'description' => $this->description,
            'total_debit' => $this->total_debit,
            'total_credit' => $this->total_credit,
            'is_posted' => $this->is_posted,
            'posted_at' => $this->posted_at,
            'is_balanced' => $this->isBalanced(),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'lines' => $this->lines->map(function ($line) {
                return [
                    'id' => $line->id,
                    'account' => [
                        'id' => $line->account->id,
                        'code' => $line->account->code,
                        'name' => $line->account->name,
                        'account_type' => $line->account->account_type,
                    ],
                    'description' => $line->description,
                    'debit_amount' => $line->debit_amount,
                    'credit_amount' => $line->credit_amount,
                    'amount' => $line->amount,
                    'type' => $line->type,
                ];
            }),
            'created_by' => [
                'id' => $this->createdBy->id,
                'name' => $this->createdBy->name,
                'email' => $this->createdBy->email,
            ],
            'posted_by' => $this->when($this->postedBy, [
                'id' => $this->postedBy->id,
                'name' => $this->postedBy->name,
                'email' => $this->postedBy->email,
            ]),
        ];
    }
}
