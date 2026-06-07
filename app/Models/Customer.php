<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_code',
        'cust_ref',
        'name',
        'contact_person',
        'phone',
        'email',
        'fax',
        'address',
        'city',
        'state',
        'postal_code',
        'country',
        'curr_code',
        'credit_limit',
        'discount',
        'pymt_discount',
        'payment_terms',
        'tax_id',
        'status',
        'sales_group_id',
        'sales_type_id',
        'sales_person_id',
        'credit_status_id',
        'dimension_id',
        'dimension2_id',
        'notes',
    ];

    protected $casts = [
        'credit_limit' => 'decimal:2',
        'discount' => 'decimal:2',
        'pymt_discount' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function salesGroup(): BelongsTo
    {
        return $this->belongsTo(SalesGroup::class);
    }

    public function salesType(): BelongsTo
    {
        return $this->belongsTo(SalesType::class);
    }

    public function salesPerson(): BelongsTo
    {
        return $this->belongsTo(SalesPerson::class);
    }

    public function creditStatus(): BelongsTo
    {
        return $this->belongsTo(CreditStatus::class);
    }

    public function branches(): HasMany
    {
        return $this->hasMany(CustomerBranch::class);
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

    public function scopeOnHold($query)
    {
        return $query->where('status', 'hold');
    }

    public function getFormattedCreditLimitAttribute(): string
    {
        return '$' . number_format($this->credit_limit, 2);
    }

    public function getFullNameAttribute(): string
    {
        return $this->name . ' (' . $this->customer_code . ')';
    }
}
