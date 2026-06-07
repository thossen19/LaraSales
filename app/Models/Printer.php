<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Printer extends Model
{
    protected $fillable = [
        'name',
        'description',
        'queue',
        'host',
        'port',
        'timeout',
    ];

    protected $casts = [
        'port' => 'integer',
        'timeout' => 'integer',
    ];
}
