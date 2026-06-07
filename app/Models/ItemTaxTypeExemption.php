<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemTaxTypeExemption extends Model
{
    protected $fillable = ['item_tax_type_id', 'tax_type_id'];

    public function itemTaxType()
    {
        return $this->belongsTo(ItemTaxType::class);
    }

    public function taxType()
    {
        return $this->belongsTo(TaxType::class);
    }
}
