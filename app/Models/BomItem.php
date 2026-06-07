<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BomItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'bill_of_materials_id',
        'component_item_id',
        'quantity',
        'unit_of_measure',
        'scrap_percentage',
        'effective_quantity',
        'notes',
        'sequence',
    ];

    protected $casts = [
        'quantity' => 'decimal:3',
        'scrap_percentage' => 'decimal:2',
        'effective_quantity' => 'decimal:3',
    ];

    public function billOfMaterials()
    {
        return $this->belongsTo(BillOfMaterials::class);
    }

    public function componentItem()
    {
        return $this->belongsTo(Item::class, 'component_item_id');
    }

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($bomItem) {
            if ($bomItem->scrap_percentage > 0) {
                $bomItem->effective_quantity = $bomItem->quantity * (1 + ($bomItem->scrap_percentage / 100));
            } else {
                $bomItem->effective_quantity = $bomItem->quantity;
            }
        });
    }
}
