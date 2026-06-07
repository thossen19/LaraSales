<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaxGroup extends Model
{
    protected $fillable = ['name', 'inactive'];

    protected $casts = ['inactive' => 'boolean'];

    public function items()
    {
        return $this->hasMany(TaxGroupItem::class);
    }
}
