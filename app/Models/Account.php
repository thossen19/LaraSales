<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'code',
        'name',
        'description',
        'account_type',
        'account_category',
        'parent_code',
        'level',
        'opening_balance',
        'current_balance',
        'is_active',
        'is_contra',
        'notes',
    ];

    protected $casts = [
        'opening_balance' => 'decimal:2',
        'current_balance' => 'decimal:2',
        'is_active' => 'boolean',
        'is_contra' => 'boolean',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function journalEntryLines()
    {
        return $this->hasMany(JournalEntryLine::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('account_type', $type);
    }

    public function scopeAssets($query)
    {
        return $query->where('account_type', 'asset');
    }

    public function scopeLiabilities($query)
    {
        return $query->where('account_type', 'liability');
    }

    public function scopeEquity($query)
    {
        return $query->where('account_type', 'equity');
    }

    public function scopeRevenue($query)
    {
        return $query->where('account_type', 'revenue');
    }

    public function scopeExpenses($query)
    {
        return $query->where('account_type', 'expense');
    }

    public function parent()
    {
        return $this->belongsTo(Account::class, 'parent_code', 'code');
    }

    public function children()
    {
        return $this->hasMany(Account::class, 'parent_code', 'code');
    }
}
