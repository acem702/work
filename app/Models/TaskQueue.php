<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskQueue extends Model
{
    protected $fillable = [
        'user_id', 'product_id', 'sequence_order', 'status',
        'assigned_at', 'activated_at', 'completed_at'
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
        'activated_at' => 'datetime',
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

    public function task()
    {
        return $this->hasOne(Task::class);
    }

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

    public function activate()
    {
        $this->update([
            'status' => 'active',
            'activated_at' => now()
        ]);
    }

    public function complete()
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now()
        ]);
    }
}