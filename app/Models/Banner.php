<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Banner extends Model
{
    use HasFactory;

    protected $fillable = [
        'apartment_id',
        'title',
        'subtitle',
        'image_path',
        'link_url',
        'link_type',
        'display_order',
        'is_active',
        'starts_at',
        'expires_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    /**
     * Get the apartment that owns the banner.
     */
    public function apartment()
    {
        return $this->belongsTo(Apartment::class);
    }

    /**
     * Scope to get only active banners.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('starts_at')
                    ->orWhere('starts_at', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            });
    }

    /**
     * Scope to order by display order.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('display_order', 'asc');
    }

    /**
     * Get the full URL for the banner image.
     */
    public function getImageUrlAttribute()
    {
        if ($this->image_path) {
            return Storage::url($this->image_path);
        }
        return null;
    }

    /**
     * Check if banner is currently active (considering dates).
     */
    public function isCurrentlyActive()
    {
        if (!$this->is_active) {
            return false;
        }

        if ($this->starts_at && $this->starts_at > now()) {
            return false;
        }

        if ($this->expires_at && $this->expires_at <= now()) {
            return false;
        }

        return true;
    }

    /**
     * Get status label for admin display.
     */
    public function getStatusLabelAttribute()
    {
        if (!$this->is_active) {
            return 'Inactive';
        }

        if ($this->starts_at && $this->starts_at > now()) {
            return 'Scheduled';
        }

        if ($this->expires_at && $this->expires_at <= now()) {
            return 'Expired';
        }

        return 'Active';
    }

    /**
     * Get status color for admin display.
     */
    public function getStatusColorAttribute()
    {
        return match($this->status_label) {
            'Active' => 'green',
            'Scheduled' => 'blue',
            'Expired' => 'red',
            'Inactive' => 'gray',
            default => 'gray',
        };
    }
}

