<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupplierInvoiceItem extends Model
{
    protected $fillable = [
        'supplier_invoice_id', 'item_id', 'stock_id', 'description',
        'quantity', 'unit_price', 'unit', 'line_total',
        'gl_code', 'dimension_id', 'dimension2_id',
    ];

    protected $casts = [
        'quantity' => 'decimal:4',
        'unit_price' => 'decimal:4',
        'line_total' => 'decimal:2',
    ];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(SupplierInvoice::class, 'supplier_invoice_id');
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }
}
