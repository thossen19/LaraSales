<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bom extends Model
{
    protected $table = 'bom';

    protected $fillable = [
        'parent',
        'component',
        'workcentre_added',
        'loc_code',
        'quantity',
    ];

    public function parentItem()
    {
        return $this->belongsTo(Item::class, 'parent', 'code');
    }

    public function componentItem()
    {
        return $this->belongsTo(Item::class, 'component', 'code');
    }

    public function workCentre()
    {
        return $this->belongsTo(WorkCentre::class, 'workcentre_added');
    }

    public function location()
    {
        return $this->belongsTo(Location::class, 'loc_code', 'loc_code');
    }
}
