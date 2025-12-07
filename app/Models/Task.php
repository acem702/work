<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable = [
        'user_id',
        'product_id',
        'task_queue_id',
        'combo_task_id',
        'combo_sequence',
        'next_combo_task_id',
        'status',
        'points_locked',
        'commission_earned',
        'balance_before',
        'balance_after',
        'submitted_at',
        'completed_at'
    ];

    protected $casts = [
        'points_locked' => 'decimal:2',
        'commission_earned' => 'decimal:2',
        'balance_before' => 'decimal:2',
        'balance_after' => 'decimal:2',
        'submitted_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    // Existing Relationships
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

    // New Combo Relationships
    public function comboTask()
    {
        return $this->belongsTo(ComboTask::class);
    }

    public function nextComboTask()
    {
        return $this->belongsTo(Task::class, 'next_combo_task_id');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    // Existing Methods
    public function complete()
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now()
        ]);
    }

    /**
     * Check if task can be submitted
     * CRITICAL: User balance must be >= 0 to prevent deficit submissions
     */
    public function canBeSubmitted()
    {
        if ($this->status !== 'pending') {
            return false;
        }
        
        // UPDATED: Check user's CURRENT balance, not future balance
        // User can only submit if they have been topped up to cover the deficit
        $user = $this->user->fresh(); // Always get fresh data
        
        // User must have balance >= 0 (meaning deficit has been covered by admin)
        return $user->point_balance >= 0;
    }

    // New Helper Methods for Combo Support
    
    /**
     * Check if this is a combo task
     */
    public function isComboTask()
    {
        return !is_null($this->combo_task_id);
    }

    /**
     * Get display name for the task
     */
    public function getDisplayNameAttribute()
    {
        if ($this->isComboTask() && $this->comboTask) {
            return "{$this->product->name} (Combo: {$this->comboTask->name} - Step {$this->combo_sequence})";
        }
        return $this->product->name;
    }

    /**
     * Get the commission for this task
     */
    public function getCalculatedCommissionAttribute()
    {
        if ($this->status === 'completed') {
            return $this->commission_earned;
        }
        return $this->product->calculateCommission($this->user);
    }

    /**
     * Check if this is the first task in a combo
     */
    public function isFirstComboTask()
    {
        return $this->isComboTask() && $this->combo_sequence === 1;
    }

    /**
     * Check if this is the last task in a combo
     */
    public function isLastComboTask()
    {
        if (!$this->isComboTask()) {
            return false;
        }
        return $this->combo_sequence === $this->comboTask->sequence_count;
    }

    /**
     * Get the next combo task sequence number
     */
    public function getNextComboSequenceAttribute()
    {
        if (!$this->isComboTask()) {
            return null;
        }
        return $this->combo_sequence + 1;
    }
}