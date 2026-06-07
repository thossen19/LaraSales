<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductionOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'item_id',
        'warehouse_id',
        'order_number',
        'order_date',
        'start_date',
        'finish_date',
        'status',
        'quantity_planned',
        'quantity_produced',
        'quantity_scrapped',
        'standard_cost',
        'actual_cost',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'order_date' => 'date',
        'start_date' => 'date',
        'finish_date' => 'date',
        'standard_cost' => 'decimal:2',
        'actual_cost' => 'decimal:2',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopePlanned($query)
    {
        return $query->where('status', 'planned');
    }

    public function scopeReleased($query)
    {
        return $query->where('status', 'released');
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    public function getProgressPercentageAttribute()
    {
        if ($this->quantity_planned == 0) {
            return 0;
        }

        return ($this->quantity_produced / $this->quantity_planned) * 100;
    }

    public function getRemainingQuantityAttribute()
    {
        return $this->quantity_planned - $this->quantity_produced;
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            $order->order_number = self::generateOrderNumber($order->company_id);
        });
    }

    private static function generateOrderNumber($companyId)
    {
        $prefix = 'PROD-' . $companyId . '-';
        $lastOrder = self::where('company_id', $companyId)
            ->where('order_number', 'like', $prefix . '%')
            ->orderBy('order_number', 'desc')
            ->first();

        if ($lastOrder) {
            $lastNumber = (int) str_replace($prefix, '', $lastOrder->order_number);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . str_pad($newNumber, 6, '0', STR_PAD_LEFT);
    }
}
