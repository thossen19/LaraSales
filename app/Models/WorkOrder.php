<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkOrder extends Model
{
    protected $table = 'work_orders';

    protected $fillable = [
        'wo_ref',
        'loc_code',
        'units_reqd',
        'stock_id',
        'date_',
        'type',
        'required_by',
        'released_date',
        'units_issued',
        'closed',
        'released',
        'additional_costs',
        'labour_cost',
        'cr_acc',
        'cr_lab_acc',
        'memo',
    ];

    protected $casts = [
        'closed' => 'boolean',
        'released' => 'boolean',
        'date_' => 'date',
        'required_by' => 'date',
        'released_date' => 'date',
    ];

    public function item()
    {
        return $this->belongsTo(Item::class, 'stock_id', 'code');
    }

    public function location()
    {
        return $this->belongsTo(Location::class, 'loc_code', 'loc_code');
    }
}
