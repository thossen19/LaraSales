<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CustomerBranch extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'branch_name',
        'branch_ref',
        'branch_code',
        'contact_person',
        'contact_name',
        'phone',
        'phone2',
        'email',
        'fax',
        'rep_lang',
        'address',
        'br_post_address',
        'city',
        'state',
        'postal_code',
        'country',
        'credit_limit',
        'payment_terms',
        'sales_person_id',
        'area_id',
        'group_no',
        'default_location',
        'default_ship_via',
        'tax_group_id',
        'bank_account',
        'notes',
        'inactive',
    ];

    protected $casts = [
        'credit_limit' => 'decimal:2',
        'inactive' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function salesPerson(): BelongsTo
    {
        return $this->belongsTo(SalesPerson::class);
    }

    public function salesArea(): BelongsTo
    {
        return $this->belongsTo(SalesArea::class, 'area_id');
    }

    public function salesGroup(): BelongsTo
    {
        return $this->belongsTo(SalesGroup::class, 'group_no');
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class, 'default_location');
    }

    public function shipper(): BelongsTo
    {
        return $this->belongsTo(Shipper::class, 'default_ship_via');
    }

    public function taxGroup(): BelongsTo
    {
        return $this->belongsTo(TaxGroup::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(SalesOrder::class, 'customer_branch_id');
    }

    public function getFormattedCreditLimitAttribute(): string
    {
        return '$' . number_format($this->credit_limit, 2);
    }

    public function getFullNameAttribute(): string
    {
        return $this->branch_name . ' (' . $this->branch_code . ')';
    }
}
