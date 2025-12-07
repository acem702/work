<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskQueue extends Model
{
    protected $fillable = [
        'user_id',
        'product_id',
        'combo_task_id',
        'is_combo',
        'sequence_order',
        'status',
        'assigned_at',
        'activated_at',
        'completed_at',
    ];

    protected $casts = [
        'is_combo' => 'boolean',
        'assigned_at' => 'datetime',
        'activated_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($taskQueue) {
            if (!$taskQueue->assigned_at) {
                $taskQueue->assigned_at = now();
            }
        });
    }

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function comboTask()
    {
        return $this->belongsTo(ComboTask::class);
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    // Scopes
    public function scopeQueued($query)
    {
        return $query->where('status', 'queued');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sequence_order');
    }

    // Methods
    public function activate()
    {
        $this->update([
            'status' => 'active',
            'activated_at' => now(),
        ]);
    }

    public function complete()
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);
    }

    /**
     * Get display name for the task queue
     */
    public function getDisplayNameAttribute()
    {
        if ($this->is_combo) {
            return $this->comboTask->name;
        }
        return $this->product->name;
    }

    /**
     * Get total points required
     */
    public function getTotalPointsAttribute()
    {
        if ($this->is_combo) {
            return $this->comboTask->total_base_points;
        }
        return $this->product->base_points;
    }
}