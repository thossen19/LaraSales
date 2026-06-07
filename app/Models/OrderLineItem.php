<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderLineItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'item_code',
        'description',
        'quantity',
        'delivered_quantity',
        'unit_price',
        'tax_rate',
        'tax_amount',
        'line_total',
        'discount_percentage',
        'discount_amount',
        'notes',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'delivered_quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'line_total' => 'decimal:2',
        'discount_percentage' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(SalesOrder::class, 'order_id');
    }

    public function getFormattedUnitPriceAttribute(): string
    {
        return '$' . number_format($this->unit_price, 2);
    }

    public function getFormattedLineTotalAttribute(): string
    {
        return '$' . number_format($this->line_total, 2);
    }

    public function getFormattedTaxRateAttribute(): string
    {
        return number_format($this->tax_rate, 2) . '%';
    }

    public function getFormattedDiscountAttribute(): string
    {
        return number_format($this->discount_percentage, 2) . '%';
    }

    public function getPendingDeliveryAttribute(): int
    {
        return $this->quantity - $this->delivered_quantity;
    }

    public function getDeliveryProgressAttribute(): string
    {
        if ($this->quantity == 0) return '0%';
        
        $delivered = ($this->delivered_quantity / $this->quantity) * 100;
        return number_format($delivered, 1) . '%';
    }
}
