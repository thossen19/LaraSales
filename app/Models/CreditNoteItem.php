<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CreditNoteItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'credit_note_id',
        'item_code',
        'description',
        'quantity',
        'unit_price',
        'discount_percentage',
        'discount_amount',
        'line_total',
        'tax_rate',
        'tax_amount',
    ];

    protected $casts = [
        'quantity' => 'decimal:4',
        'unit_price' => 'decimal:4',
        'discount_percentage' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'line_total' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'tax_amount' => 'decimal:2',
    ];

    public function creditNote(): BelongsTo
    {
        return $this->belongsTo(CreditNote::class, 'credit_note_id');
    }
}
