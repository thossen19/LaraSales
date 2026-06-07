<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuickEntry extends Model
{
    protected $fillable = [
        'type',
        'description',
        'usage',
        'base_amount',
        'base_desc',
        'bal_type',
    ];

    protected $casts = [
        'type' => 'integer',
        'base_amount' => 'float',
        'bal_type' => 'boolean',
    ];

    public function lines()
    {
        return $this->hasMany(QuickEntryLine::class, 'qid');
    }
}