<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shipper extends Model
{
    protected $primaryKey = 'shipper_id';

    protected $fillable = [
        'shipper_name',
        'contact',
        'phone',
        'phone2',
        'address',
        'inactive',
    ];

    protected $casts = [
        'inactive' => 'boolean',
    ];
}
