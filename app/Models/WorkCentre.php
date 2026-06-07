<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkCentre extends Model
{
    protected $table = 'work_centres';

    protected $fillable = [
        'name',
        'description',
        'inactive',
    ];

    protected $casts = [
        'inactive' => 'boolean',
    ];
}
