<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesPoint extends Model
{
    protected $table = 'sales_pos';

    protected $fillable = [
        'pos_name',
        'pos_location',
        'pos_account',
        'cash_sale',
        'credit_sale',
        'inactive',
    ];

    protected $casts = [
        'cash_sale' => 'boolean',
        'credit_sale' => 'boolean',
        'inactive' => 'boolean',
    ];
}
