<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactCategory extends Model
{
    protected $table = 'crm_categories';

    protected $fillable = [
        'type',
        'action',
        'name',
        'description',
        'inactive',
        'system',
    ];

    protected $casts = [
        'inactive' => 'boolean',
        'system' => 'boolean',
    ];
}
