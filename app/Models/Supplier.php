<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'name',
        'supp_ref',
        'email',
        'phone',
        'mobile',
        'fax',
        'contact_person',
        'phone2',
        'rep_lang',
        'address',
        'physical_address',
        'city',
        'country',
        'postal_code',
        'tax_id',
        'gst_no',
        'website',
        'supp_account_no',
        'bank_account',
        'curr_code',
        'tax_group_id',
        'tax_included',
        'supplier_type',
        'credit_limit',
        'current_balance',
        'payment_terms',
        'payment_discount_account',
        'purchase_account',
        'payable_account',
        'dimension_id',
        'dimension2_id',
        'is_active',
        'inactive',
        'notes',
    ];

    protected $casts = [
        'credit_limit' => 'decimal:2',
        'current_balance' => 'decimal:2',
        'tax_included' => 'boolean',
        'is_active' => 'boolean',
        'inactive' => 'boolean',
        'dimension_id' => 'integer',
        'dimension2_id' => 'integer',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function purchaseOrders()
    {
        return $this->hasMany(PurchaseOrder::class);
    }

    public function taxGroup(): BelongsTo
    {
        return $this->belongsTo(TaxGroup::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInactive($query)
    {
        return $query->where('inactive', true);
    }

    public function scopeIndividual($query)
    {
        return $query->where('supplier_type', 'individual');
    }

    public function scopeCompany($query)
    {
        return $query->where('supplier_type', 'company');
    }
}
