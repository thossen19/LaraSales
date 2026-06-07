<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaxGroupItem extends Model
{
    protected $fillable = ['tax_group_id', 'tax_type_id', 'tax_shipping'];

    protected $casts = ['tax_shipping' => 'boolean'];

    public function taxGroup()
    {
        return $this->belongsTo(TaxGroup::class);
    }

    public function taxType()
    {
        return $this->belongsTo(TaxType::class);
    }
}
