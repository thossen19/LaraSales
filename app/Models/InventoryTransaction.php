<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'item_id',
        'warehouse_id',
        'transaction_type',
        'reference_type',
        'reference_id',
        'quantity',
        'unit_cost',
        'total_cost',
        'quantity_before',
        'quantity_after',
        'notes',
    ];

    protected $casts = [
        'unit_cost' => 'decimal:2',
        'total_cost' => 'decimal:2',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('transaction_type', $type);
    }

    public function scopeIn($query)
    {
        return $query->where('transaction_type', 'in');
    }

    public function scopeOut($query)
    {
        return $query->where('transaction_type', 'out');
    }
}
