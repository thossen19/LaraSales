<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FixedAsset extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'asset_number',
        'name',
        'description',
        'category',
        'location',
        'purchase_date',
        'purchase_cost',
        'current_value',
        'depreciation_method',
        'useful_life_years',
        'salvage_value',
        'depreciation_start_date',
        'accumulated_depreciation',
        'status',
        'responsible_person',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'purchase_cost' => 'decimal:2',
        'current_value' => 'decimal:2',
        'salvage_value' => 'decimal:2',
        'depreciation_start_date' => 'date',
        'accumulated_depreciation' => 'decimal:2',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function depreciationRecords()
    {
        return $this->hasMany(DepreciationRecord::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeDisposed($query)
    {
        return $query->where('status', 'disposed');
    }

    public function scopeFullyDepreciated($query)
    {
        return $query->where('status', 'fully_depreciated');
    }

    public function getAnnualDepreciationAttribute()
    {
        $depreciableAmount = $this->purchase_cost - $this->salvage_value;
        
        switch ($this->depreciation_method) {
            case 'straight_line':
                return $depreciableAmount / $this->useful_life_years;
            
            case 'declining_balance':
                $rate = 2 / $this->useful_life_years;
                return $this->current_value * $rate;
            
            default:
                return 0;
        }
    }

    public function getMonthlyDepreciationAttribute()
    {
        return $this->annual_depreciation / 12;
    }

    public function getRemainingLifeAttribute()
    {
        $yearsPassed = now()->diffInYears($this->depreciation_start_date);
        return max(0, $this->useful_life_years - $yearsPassed);
    }

    public function getDepreciationPercentageAttribute()
    {
        if ($this->purchase_cost == 0) {
            return 0;
        }

        return ($this->accumulated_depreciation / ($this->purchase_cost - $this->salvage_value)) * 100;
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($asset) {
            $asset->asset_number = self::generateAssetNumber($asset->company_id);
            $asset->current_value = $asset->purchase_cost;
        });
    }

    private static function generateAssetNumber($companyId)
    {
        $prefix = 'AST-' . $companyId . '-';
        $lastAsset = self::where('company_id', $companyId)
            ->where('asset_number', 'like', $prefix . '%')
            ->orderBy('asset_number', 'desc')
            ->first();

        if ($lastAsset) {
            $lastNumber = (int) str_replace($prefix, '', $lastAsset->asset_number);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . str_pad($newNumber, 6, '0', STR_PAD_LEFT);
    }
}
