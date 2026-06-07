<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesOrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'sales_order_id',
        'item_id',
        'warehouse_id',
        'description',
        'quantity',
        'unit_price',
        'discount_percentage',
        'discount_amount',
        'tax_percentage',
        'tax_amount',
        'subtotal',
        'total',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'discount_percentage' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'tax_percentage' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function salesOrder()
    {
        return $this->belongsTo(SalesOrder::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($item) {
            $lineSubtotal = $item->unit_price * $item->quantity;
            $lineDiscount = $lineSubtotal * ($item->discount_percentage / 100);
            $lineAfterDiscount = $lineSubtotal - $lineDiscount;
            $lineTax = $lineAfterDiscount * ($item->tax_percentage / 100);
            $lineTotal = $lineAfterDiscount + $lineTax;

            $item->subtotal = $lineSubtotal;
            $item->discount_amount = $lineDiscount;
            $item->tax_amount = $lineTax;
            $item->total = $lineTotal;
        });
    }
}
