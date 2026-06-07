<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SalesGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'group_name',
        'description',
        'discount_percentage',
        'status',
    ];

    protected $casts = [
        'discount_percentage' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function customers(): HasMany
    {
        return $this->hasMany(Customer::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }

    public function getFormattedDiscountAttribute(): string
    {
        return number_format($this->discount_percentage, 2) . '%';
    }
}
