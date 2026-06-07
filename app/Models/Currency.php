<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    protected $primaryKey = 'curr_abrev';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'curr_abrev',
        'curr_symbol',
        'currency',
        'country',
        'hundreds_name',
        'auto_update',
        'inactive',
    ];

    protected $casts = [
        'auto_update' => 'boolean',
        'inactive' => 'boolean',
    ];
}