<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ComboTask extends Model
{
    protected $fillable = [
        'name',
        'description',
        'total_base_points',
        'sequence_count',
        'is_active',
    ];

    protected $casts = [
        'total_base_points' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function taskQueues()
    {
        return $this->hasMany(TaskQueue::class, 'combo_task_id');
    }


    /**
     * Get combo task items in sequence order
     */
    public function items()
    {
        return $this->hasMany(ComboTaskItem::class)->orderBy('sequence_order');
    }

    /**
     * Get total points required for combo
     */
    public function getTotalPointsAttribute()
    {
        return $this->items->sum(function ($item) {
            return $item->product->base_points;
        });
    }

    /**
     * Check if combo is accessible by user
     */
    public function isAccessibleBy(User $user)
    {
        // All products in combo must be accessible
        foreach ($this->items as $item) {
            if (!$item->product->isAccessibleBy($user)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Calculate total commission for combo
     */
    public function calculateTotalCommission(User $user)
    {
        return $this->items->sum(function ($item) use ($user) {
            return $item->product->calculateCommission($user);
        });
    }

    /**
     * Scope: Only active combo tasks
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

}