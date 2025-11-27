<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    protected $fillable = [
        'name', 'phone', 'password', 'withdrawal_password', 'role', 'membership_tier_id',
        'point_balance', 'withdrawal_address', 'exchanger',  'referrer_id', 'referral_code', 'status',
        'last_task_date', 'tasks_completed_today'
    ];

    protected $hidden = ['password', 'withdrawal_password', 'remember_token'];

    protected $casts = [
        'last_task_date' => 'datetime',
        'point_balance' => 'decimal:2',
    ];

    // Relationships
    public function membershipTier()
    {
        return $this->belongsTo(MembershipTier::class);
    }

    public function referrer()
    {
        return $this->belongsTo(User::class, 'referrer_id');
    }

    public function referrals()
    {
        return $this->hasMany(User::class, 'referrer_id');
    }

    public function taskQueues()
    {
        return $this->hasMany(TaskQueue::class);
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function referralEarnings()
    {
        return $this->hasMany(ReferralEarning::class, 'referrer_id');
    }

    public function withdrawals()
    {
        return $this->hasMany(Withdrawal::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeAgents($query)
    {
        return $query->where('role', 'agent');
    }

    // Helper Methods
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isAgent()
    {
        return $this->role === 'agent';
    }

    public function canPerformTask()
    {
        // Reset daily counter if new day
        if ($this->last_task_date?->isToday() === false) {
            $this->tasks_completed_today = 0;
            $this->last_task_date = now();
            $this->save();
        }

        return $this->tasks_completed_today < $this->membershipTier->daily_task_limit;
    }

    public function hasActivePendingTask()
    {
        return $this->tasks()->where('status', 'pending')->exists();
    }

    public function hasSufficientBalance($amount)
    {
        return $this->point_balance >= $amount;
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            if (empty($user->referral_code)) {
                $user->referral_code = strtoupper(Str::random(8));
            }
        });
    }
}