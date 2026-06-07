<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkCenter extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'code',
        'name',
        'description',
        'location',
        'hourly_rate',
        'capacity_hours',
        'efficiency_percentage',
        'is_active',
        'notes',
    ];

    protected $casts = [
        'hourly_rate' => 'decimal:2',
        'capacity_hours' => 'decimal:2',
        'efficiency_percentage' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function getEffectiveCapacityAttribute()
    {
        return $this->capacity_hours * ($this->efficiency_percentage / 100);
    }
}
