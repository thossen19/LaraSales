<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dimension extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'code',
        'name',
        'description',
        'type',
        'parent_id',
        'manager',
        'budget_code',
        'budget_amount',
        'actual_amount',
        'is_active',
        'notes',
    ];

    protected $casts = [
        'budget_amount' => 'decimal:2',
        'actual_amount' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function parent()
    {
        return $this->belongsTo(Dimension::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Dimension::class, 'parent_id');
    }

    public function dimensionAssignments()
    {
        return $this->hasMany(DimensionAssignment::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeDepartments($query)
    {
        return $query->where('type', 'department');
    }

    public function scopeProjects($query)
    {
        return $query->where('type', 'project');
    }

    public function scopeCostCenters($query)
    {
        return $query->where('type', 'cost_center');
    }

    public function scopeLocations($query)
    {
        return $query->where('type', 'location');
    }

    public function getVarianceAttribute()
    {
        return $this->budget_amount - $this->actual_amount;
    }

    public function getVariancePercentageAttribute()
    {
        if ($this->budget_amount == 0) {
            return 0;
        }

        return ($this->variance / $this->budget_amount) * 100;
    }

    public function getFullPathAttribute()
    {
        $path = [$this->name];
        $parent = $this->parent;

        while ($parent) {
            array_unshift($path, $parent->name);
            $parent = $parent->parent;
        }

        return implode(' > ', $path);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($dimension) {
            $dimension->code = self::generateDimensionCode($dimension->company_id, $dimension->type);
        });
    }

    private static function generateDimensionCode($companyId, $type)
    {
        $prefix = strtoupper(substr($type, 0, 3)) . '-' . $companyId . '-';
        $lastDimension = self::where('company_id', $companyId)
            ->where('type', $type)
            ->where('code', 'like', $prefix . '%')
            ->orderBy('code', 'desc')
            ->first();

        if ($lastDimension) {
            $lastNumber = (int) str_replace($prefix, '', $lastDimension->code);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }
}
