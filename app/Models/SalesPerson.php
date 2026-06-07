<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalesPerson extends Model
{
    use HasFactory;

    protected $table = 'sales_persons';

    protected $fillable = [
        'name',
        'email',
        'phone',
        'fax',
        'commission_rate',
        'monthly_target',
        'provision2',
        'sales_area_id',
        'status',
    ];

    protected $casts = [
        'commission_rate' => 'decimal:2',
        'monthly_target' => 'decimal:2',
        'provision2' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function salesArea(): BelongsTo
    {
        return $this->belongsTo(SalesArea::class);
    }

    public function customers(): HasMany
    {
        return $this->hasMany(Customer::class);
    }

    public function quotations(): HasMany
    {
        return $this->hasMany(SalesQuotation::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(SalesOrder::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }

    public function getFormattedCommissionAttribute(): string
    {
        return number_format($this->commission_rate, 2) . '%';
    }

    public function getFormattedTargetAttribute(): string
    {
        return '$' . number_format($this->monthly_target, 2);
    }
}
