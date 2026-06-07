<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'code',
        'name',
        'description',
        'long_description',
        'category',
        'unit_of_measure',
        'mb_flag',
        'tax_type_id',
        'purchase_price',
        'sale_price',
        'cost_price',
        'purchase_cost',
        'depreciation_method',
        'depreciation_rate',
        'depreciation_factor',
        'depreciation_start',
        'depreciation_date',
        'fa_class_id',
        'material_cost',
        'labour_cost',
        'overhead_cost',
        'weight',
        'volume',
        'barcode',
        'image',
        'reorder_level',
        'reorder_quantity',
        'is_active',
        'is_stock_item',
        'is_service',
        'sales_account',
        'purchase_account',
        'inventory_account',
        'cogs_account',
        'adjustment_account',
        'wip_account',
        'dimension_id',
        'dimension2_id',
        'no_sale',
        'no_purchase',
        'editable',
        'notes',
    ];

    protected $casts = [
        'purchase_price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'cost_price' => 'decimal:2',
        'material_cost' => 'decimal:2',
        'purchase_cost' => 'decimal:2',
        'is_active' => 'boolean',
        'is_stock_item' => 'boolean',
        'is_service' => 'boolean',
        'depreciation_start' => 'date',
        'depreciation_date' => 'date',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function salesOrderItems()
    {
        return $this->hasMany(SalesOrderItem::class);
    }

    public function purchaseOrderItems()
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    public function inventoryTransactions()
    {
        return $this->hasMany(InventoryTransaction::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeStockItem($query)
    {
        return $query->where('is_stock_item', true);
    }

    public function scopeService($query)
    {
        return $query->where('is_service', true);
    }

    public function getCurrentStockAttribute()
    {
        return $this->inventoryTransactions()
            ->where('warehouse_id', $this->defaultWarehouseId ?? 1)
            ->latest()
            ->first()
            ->quantity_after ?? 0;
    }
}
