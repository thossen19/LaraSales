<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Refline extends Model
{
    protected $fillable = [
        'trans_type',
        'prefix',
        'pattern',
        'description',
        'default',
        'inactive',
    ];

    protected $casts = [
        'default' => 'boolean',
        'inactive' => 'boolean',
        'trans_type' => 'integer',
    ];
}
