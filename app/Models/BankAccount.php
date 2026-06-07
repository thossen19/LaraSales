<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BankAccount extends Model
{
    protected $fillable = [
        'account_code',
        'account_type',
        'bank_account_name',
        'bank_account_number',
        'bank_name',
        'bank_address',
        'bank_curr_code',
        'dflt_curr_act',
        'bank_charge_act',
        'inactive',
    ];

    protected $casts = [
        'account_type' => 'integer',
        'dflt_curr_act' => 'boolean',
        'inactive' => 'boolean',
    ];

    public function glAccount()
    {
        return $this->belongsTo(Account::class, 'account_code', 'code');
    }

    public function chargeAccount()
    {
        return $this->belongsTo(Account::class, 'bank_charge_act', 'code');
    }
}
