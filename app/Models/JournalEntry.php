<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JournalEntry extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'entry_number',
        'entry_date',
        'reference_type',
        'reference_id',
        'description',
        'total_debit',
        'total_credit',
        'is_posted',
        'posted_at',
        'posted_by',
        'created_by',
    ];

    protected $casts = [
        'entry_date' => 'date',
        'total_debit' => 'decimal:2',
        'total_credit' => 'decimal:2',
        'is_posted' => 'boolean',
        'posted_at' => 'datetime',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function lines()
    {
        return $this->hasMany(JournalEntryLine::class);
    }

    public function postedBy()
    {
        return $this->belongsTo(User::class, 'posted_by');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopePosted($query)
    {
        return $query->where('is_posted', true);
    }

    public function scopeDraft($query)
    {
        return $query->where('is_posted', false);
    }

    public function scopeByReference($query, $type, $id)
    {
        return $query->where('reference_type', $type)->where('reference_id', $id);
    }

    public function isBalanced()
    {
        return abs($this->total_debit - $this->total_credit) < 0.01;
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($entry) {
            $entry->entry_number = self::generateEntryNumber($entry->company_id);
        });
    }

    private static function generateEntryNumber($companyId)
    {
        $prefix = 'JE-' . $companyId . '-';
        $lastEntry = self::where('company_id', $companyId)
            ->where('entry_number', 'like', $prefix . '%')
            ->orderBy('entry_number', 'desc')
            ->first();

        if ($lastEntry) {
            $lastNumber = (int) str_replace($prefix, '', $lastEntry->entry_number);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . str_pad($newNumber, 6, '0', STR_PAD_LEFT);
    }
}
