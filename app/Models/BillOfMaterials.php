<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BillOfMaterials extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'item_id',
        'bom_number',
        'version',
        'status',
        'standard_cost',
        'notes',
        'is_default',
        'effective_date',
        'created_by',
    ];

    protected $casts = [
        'standard_cost' => 'decimal:2',
        'is_default' => 'boolean',
        'effective_date' => 'date',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function bomItems()
    {
        return $this->hasMany(BomItem::class);
    }

    public function components()
    {
        return $this->belongsToMany(Item::class, 'bom_items', 'bill_of_materials_id', 'component_item_id')
            ->withPivot(['quantity', 'unit_of_measure', 'scrap_percentage', 'effective_quantity', 'notes', 'sequence'])
            ->orderBy('pivot_sequence');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($bom) {
            $bom->bom_number = self::generateBomNumber($bom->company_id);
        });

        static::created(function ($bom) {
            if ($bom->is_default) {
                BillOfMaterials::where('item_id', $bom->item_id)
                    ->where('id', '!=', $bom->id)
                    ->update(['is_default' => false]);
            }
        });

        static::updated(function ($bom) {
            if ($bom->is_default) {
                BillOfMaterials::where('item_id', $bom->item_id)
                    ->where('id', '!=', $bom->id)
                    ->update(['is_default' => false]);
            }
        });
    }

    private static function generateBomNumber($companyId)
    {
        $prefix = 'BOM-' . $companyId . '-';
        $lastBom = self::where('company_id', $companyId)
            ->where('bom_number', 'like', $prefix . '%')
            ->orderBy('bom_number', 'desc')
            ->first();

        if ($lastBom) {
            $lastNumber = (int) str_replace($prefix, '', $lastBom->bom_number);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . str_pad($newNumber, 6, '0', STR_PAD_LEFT);
    }
}
