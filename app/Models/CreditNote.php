<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CreditNote extends Model
{
    use HasFactory;

    protected $fillable = [
        'credit_note_number',
        'customer_id',
        'customer_branch_id',
        'sales_order_id',
        'credit_date',
        'reference',
        'subtotal',
        'tax_amount',
        'total_amount',
        'discount_amount',
        'reason',
        'memo',
        'status',
        'created_by',
    ];

    protected $casts = [
        'credit_date' => 'date',
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(CustomerBranch::class, 'customer_branch_id');
    }

    public function originalInvoice(): BelongsTo
    {
        return $this->belongsTo(SalesOrder::class, 'sales_order_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(CreditNoteItem::class, 'credit_note_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
