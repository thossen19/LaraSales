<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemTaxType extends Model
{
    protected $fillable = ['name', 'exempt', 'inactive'];

    protected $casts = [
        'exempt' => 'boolean',
        'inactive' => 'boolean',
    ];

    public function exemptions()
    {
        return $this->hasMany(ItemTaxTypeExemption::class);
    }
}
