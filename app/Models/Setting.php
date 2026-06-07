<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'key',
        'value',
        'type',
        'category',
        'description',
        'is_public',
    ];

    protected $casts = [
        'is_public' => 'boolean',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    public function scopePrivate($query)
    {
        return $query->where('is_public', false);
    }

    public function getTypedValueAttribute()
    {
        switch ($this->type) {
            case 'boolean':
                return filter_var($this->value, FILTER_VALIDATE_BOOLEAN);
            case 'number':
                return is_numeric($this->value) ? (float) $this->value : 0;
            case 'json':
                return json_decode($this->value, true) ?? [];
            default:
                return $this->value;
        }
    }

    public static function getSetting($key, $companyId, $default = null)
    {
        $setting = self::where('company_id', $companyId)
            ->where('key', $key)
            ->first();

        if ($setting) {
            return $setting->typed_value;
        }

        return $default;
    }

    public static function setSetting($key, $value, $companyId, $type = 'string', $category = 'general', $description = null, $isPublic = false)
    {
        return self::updateOrCreate(
            ['company_id' => $companyId, 'key' => $key],
            [
                'value' => is_array($value) ? json_encode($value) : $value,
                'type' => $type,
                'category' => $category,
                'description' => $description,
                'is_public' => $isPublic,
            ]
        );
    }
}
