<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class MembershipTier extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'level',
        'daily_task_limit',
        'commission_multiplier',
        'upgrade_cost',
        'description',
        'image_url',
        'is_active'
    ];

    protected $casts = [
        'commission_multiplier' => 'decimal:2',
        'upgrade_cost' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        // Auto-generate slug from name
        static::creating(function ($tier) {
            if (empty($tier->slug)) {
                $tier->slug = Str::slug($tier->name);
            }
        });

        static::updating(function ($tier) {
            if ($tier->isDirty('name')) {
                $tier->slug = Str::slug($tier->name);
            }
        });

        // Delete image when tier is deleted
        static::deleting(function ($tier) {
            if ($tier->image_url && Storage::exists($tier->image_url)) {
                Storage::delete($tier->image_url);
            }
        });
    }

    // Relationships
    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'min_membership_tier_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('level');
    }

    // Methods
    public function getNextTier()
    {
        return self::where('level', '>', $this->level)
            ->where('is_active', true)
            ->orderBy('level')
            ->first();
    }

    /**
     * Get full URL for the tier image
     */
    public function getImageUrlFullAttribute()
    {
        if (!$this->image_url) {
            // Return default placeholder image
            return asset('images/default-tier.png');
        }

        // If it's already a full URL, return as is
        if (Str::startsWith($this->image_url, ['http://', 'https://'])) {
            return $this->image_url;
        }

        // Otherwise, generate storage URL
        return Storage::url($this->image_url);
    }

    public function getActiveUsersCountAttribute()
    {
        return $this->users()->where('status', 'active')->count();
    }

    public function getAccessibleProductsCountAttribute()
    {
        return Product::whereHas('minMembershipTier', fn($q) => 
            $q->where('level', '<=', $this->level)
        )->count();
    }
}