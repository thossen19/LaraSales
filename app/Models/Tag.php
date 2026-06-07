<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    protected $fillable = [
        'type',
        'name',
        'description',
        'inactive',
    ];

    protected $casts = [
        'type' => 'integer',
        'inactive' => 'boolean',
    ];
}