<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DimensionAssignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'reference_type',
        'reference_id',
        'dimension_id',
        'effective_date',
        'end_date',
        'percentage',
        'notes',
    ];

    protected $casts = [
        'effective_date' => 'date',
        'end_date' => 'date',
        'percentage' => 'decimal:2',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function dimension()
    {
        return $this->belongsTo(Dimension::class);
    }

    public function scopeCurrent($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('end_date')
              ->orWhere('end_date', '>=', now());
        });
    }

    public function scopeEffective($query, $date)
    {
        return $query->where('effective_date', '<=', $date)
            ->where(function ($q) use ($date) {
                $q->whereNull('end_date')
                  ->orWhere('end_date', '>=', $date);
            });
    }

    public function getReferenceAttribute()
    {
        switch ($this->reference_type) {
            case 'customer':
                return Customer::find($this->reference_id);
            case 'supplier':
                return Supplier::find($this->reference_id);
            case 'item':
                return Item::find($this->reference_id);
            case 'user':
                return User::find($this->reference_id);
            case 'account':
                return Account::find($this->reference_id);
            default:
                return null;
        }
    }
}
