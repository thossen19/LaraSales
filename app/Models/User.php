<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'company_id',
        'name',
        'email',
        'password',
        'phone',
        'address',
        'avatar',
        'is_active',
        'preferences',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
        'preferences' => 'array',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function salesOrders()
    {
        return $this->hasMany(SalesOrder::class, 'created_by');
    }

    public function purchaseOrders()
    {
        return $this->hasMany(PurchaseOrder::class, 'created_by');
    }

    public function journalEntries()
    {
        return $this->hasMany(JournalEntry::class, 'created_by');
    }

    public function postedJournalEntries()
    {
        return $this->hasMany(JournalEntry::class, 'posted_by');
    }
}
