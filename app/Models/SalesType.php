<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SalesType extends Model
{
    use HasFactory;

    protected $fillable = [
        'type_name',
        'description',
        'default_tax_rate',
        'default_payment_terms',
        'status',
        'factor',
        'tax_included',
    ];

    protected $casts = [
        'default_tax_rate' => 'decimal:2',
        'default_payment_terms' => 'integer',
        'factor' => 'decimal:4',
        'tax_included' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

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

    public function getFormattedTaxRateAttribute(): string
    {
        return number_format($this->default_tax_rate, 2) . '%';
    }

    public function getFormattedPaymentTermsAttribute(): string
    {
        return $this->default_payment_terms . ' days';
    }
}
