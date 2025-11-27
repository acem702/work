<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReferralEarning extends Model
{
    protected $fillable = [
        'referrer_id', 'referee_id', 'task_id', 'amount',
        'referral_level', 'earning_type'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function referrer()
    {
        return $this->belongsTo(User::class, 'referrer_id');
    }

    public function referee()
    {
        return $this->belongsTo(User::class, 'referee_id');
    }

    public function task()
    {
        return $this->belongsTo(Task::class);
    }
}