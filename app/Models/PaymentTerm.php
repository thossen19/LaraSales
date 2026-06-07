<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentTerm extends Model
{
    protected $table = 'payment_terms';
    protected $primaryKey = 'terms_indicator';

    protected $fillable = [
        'terms',
        'days_before_due',
        'day_in_following_month',
        'inactive',
    ];

    protected $casts = [
        'inactive' => 'boolean',
        'days_before_due' => 'integer',
        'day_in_following_month' => 'integer',
    ];
}
