<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name', 'slug', 'description', 'base_points', 'base_commission',
        'min_membership_tier_id', 'image_url', 'is_active', 'total_submissions'
    ];

    protected $casts = [
        'base_points' => 'decimal:2',
        'base_commission' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function minMembershipTier()
    {
        return $this->belongsTo(MembershipTier::class, 'min_membership_tier_id');
    }

    public function taskQueues()
    {
        return $this->hasMany(TaskQueue::class);
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForMembershipTier($query, $tierLevel)
    {
        return $query->whereHas('minMembershipTier', function ($q) use ($tierLevel) {
            $q->where('level', '<=', $tierLevel);
        });
    }

    public function calculateCommission(User $user)
    {
        return $this->base_commission * $user->membershipTier->commission_multiplier;
    }

    public function isAccessibleBy(User $user)
    {
        return $user->membershipTier->level >= $this->minMembershipTier->level;
    }
}