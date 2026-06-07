<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    protected $primaryKey = 'loc_code';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'loc_code',
        'location_name',
        'delivery_address',
        'phone',
        'phone2',
        'fax',
        'email',
        'contact',
        'inactive',
        'fixed_asset',
    ];

    protected $casts = [
        'inactive' => 'boolean',
        'fixed_asset' => 'boolean',
    ];
}
