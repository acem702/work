<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MembershipTier extends Model
{
    protected $fillable = [
        'name', 'slug', 'level', 'daily_task_limit',
        'commission_multiplier', 'upgrade_cost', 'description', 'is_active'
    ];

    protected $casts = [
        'commission_multiplier' => 'decimal:2',
        'upgrade_cost' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'min_membership_tier_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('level');
    }

    public function getNextTier()
    {
        return self::where('level', '>', $this->level)
            ->where('is_active', true)
            ->orderBy('level')
            ->first();
    }
}