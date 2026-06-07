<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'city',
        'country',
        'postal_code',
        'tax_id',
        'registration_number',
        'website',
        'logo',
        'notes',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function customers()
    {
        return $this->hasMany(Customer::class);
    }

    public function suppliers()
    {
        return $this->hasMany(Supplier::class);
    }

    public function items()
    {
        return $this->hasMany(Item::class);
    }

    public function warehouses()
    {
        return $this->hasMany(Warehouse::class);
    }

    public function salesOrders()
    {
        return $this->hasMany(SalesOrder::class);
    }

    public function purchaseOrders()
    {
        return $this->hasMany(PurchaseOrder::class);
    }

    public function accounts()
    {
        return $this->hasMany(Account::class);
    }

    public function journalEntries()
    {
        return $this->hasMany(JournalEntry::class);
    }

    public function inventoryTransactions()
    {
        return $this->hasMany(InventoryTransaction::class);
    }
}
