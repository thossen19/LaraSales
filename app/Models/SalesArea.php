<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalesArea extends Model
{
    use HasFactory;

    protected $fillable = [
        'area_name',
        'area_code',
        'description',
        'region',
        'manager_id',
        'monthly_target',
        'coverage_cities',
        'office_phone',
        'office_email',
        'status',
    ];

    protected $casts = [
        'monthly_target' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function manager(): BelongsTo
    {
        return $this->belongsTo(SalesPerson::class, 'manager_id');
    }

    public function salesPersons(): HasMany
    {
        return $this->hasMany(SalesPerson::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }

    public function getFormattedTargetAttribute(): string
    {
        return '$' . number_format($this->monthly_target, 2);
    }
}
