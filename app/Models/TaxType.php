<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaxType extends Model
{
    protected $fillable = [
        'name',
        'rate',
        'sales_gl_code',
        'purchasing_gl_code',
        'inactive',
    ];

    protected $casts = [
        'rate' => 'decimal:4',
        'inactive' => 'boolean',
    ];

    public function salesGlAccount()
    {
        return $this->belongsTo(Account::class, 'sales_gl_code', 'code');
    }

    public function purchasingGlAccount()
    {
        return $this->belongsTo(Account::class, 'purchasing_gl_code', 'code');
    }
}
