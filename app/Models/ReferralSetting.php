<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReferralSetting extends Model
{
    protected $fillable = [
        'level', 'commission_percentage', 'is_active'
    ];

    protected $casts = [
        'commission_percentage' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public static function getPercentageForLevel($level)
    {
        return self::where('level', $level)
            ->where('is_active', true)
            ->value('commission_percentage') ?? 0;
    }
}