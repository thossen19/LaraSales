<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuickEntryLine extends Model
{
    protected $fillable = [
        'qid',
        'amount',
        'memo',
        'action',
        'dest_id',
        'dimension_id',
        'dimension2_id',
    ];

    protected $casts = [
        'amount' => 'float',
    ];

    public function quickEntry()
    {
        return $this->belongsTo(QuickEntry::class, 'qid');
    }

    public function glAccount()
    {
        return $this->belongsTo(Account::class, 'dest_id', 'code');
    }

    public function taxType()
    {
        return $this->belongsTo(TaxType::class, 'dest_id', 'id');
    }
}