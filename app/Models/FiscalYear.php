<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FiscalYear extends Model
{
    protected $fillable = ['begin', 'end', 'closed'];

    protected $casts = [
        'begin' => 'date',
        'end' => 'date',
        'closed' => 'boolean',
    ];
}
