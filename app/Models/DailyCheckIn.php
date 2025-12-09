<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyCheckIn extends Model
{
    protected $fillable = [
        'user_id',
        'check_in_date',
        'streak_day',
        'reward_amount',
    ];

    protected $casts = [
        'check_in_date' => 'date',
        'reward_amount' => 'decimal:2',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get reward amount for a specific day
     */
    public static function getRewardForDay($day)
    {
        // Customize rewards per day
        $rewards = [
            1 => 5,    // Day 1: $5
            2 => 10,   // Day 2: $10
            3 => 15,   // Day 3: $15
            4 => 20,   // Day 4: $20
            5 => 30,   // Day 5: $30
            6 => 50,   // Day 6: $50
            7 => 100,  // Day 7: $100 (Big reward!)
        ];

        return $rewards[$day] ?? 5;
    }

    /**
     * Get all rewards configuration
     */
    public static function getAllRewards()
    {
        return [
            1 => ['amount' => 5, 'icon' => 'fa-gift'],
            2 => ['amount' => 10, 'icon' => 'fa-gift'],
            3 => ['amount' => 15, 'icon' => 'fa-gift'],
            4 => ['amount' => 20, 'icon' => 'fa-star'],
            5 => ['amount' => 30, 'icon' => 'fa-star'],
            6 => ['amount' => 50, 'icon' => 'fa-gem'],
            7 => ['amount' => 100, 'icon' => 'fa-trophy'],
        ];
    }
}