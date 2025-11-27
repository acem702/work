<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable = [
        'user_id', 'product_id', 'task_queue_id', 'status',
        'points_locked', 'commission_earned', 'balance_before',
        'balance_after', 'submitted_at', 'completed_at'
    ];

    protected $casts = [
        'points_locked' => 'decimal:2',
        'commission_earned' => 'decimal:2',
        'balance_before' => 'decimal:2',
        'balance_after' => 'decimal:2',
        'submitted_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function taskQueue()
    {
        return $this->belongsTo(TaskQueue::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'related_task_id');
    }

    public function referralEarnings()
    {
        return $this->hasMany(ReferralEarning::class);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function complete()
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now()
        ]);
    }

    /**
     * Check if task can be submitted
     */
    public function canBeSubmitted()
    {
        if ($this->status !== 'pending') {
            return false;
        }
        
        $user = $this->user;
        $commission = $this->product->calculateCommission($user);
        $balanceAfterCompletion = $user->point_balance + $this->points_locked + $commission;
        
        // Can submit if balance after completion would be non-negative
        return $balanceAfterCompletion >= 0;
    }
}