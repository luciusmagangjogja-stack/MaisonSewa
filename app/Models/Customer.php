<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Customer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'branch_id', 'user_id', 'name', 'phone', 'email', 'address',
        'id_number', 'photo',
        'height', 'weight',
        'notes', 'is_blacklisted', 'blacklist_reason', 'opt_out',
    ];

    protected $casts = [
        'is_blacklisted' => 'boolean',
        'opt_out' => 'boolean',
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function rentals(): HasMany
    {
        return $this->hasMany(Rental::class);
    }

    public function notifications(): MorphMany
    {
        return $this->morphMany(\App\Models\Notification::class, 'notifiable');
    }

    public function getPhotoUrlAttribute(): string
    {
        if ($this->photo) {
            return asset('storage/' . $this->photo);
        }
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=E8DED1&color=2B2B2B&size=128';
    }

    public function getTotalRentalsAttribute(): int
    {
        return $this->rentals()->count();
    }

    public function getActiveRentalsAttribute()
    {
        return $this->rentals()->whereIn('rental_status', ['active', 'overdue'])->get();
    }

    public function broadcastLogs(): HasMany
    {
        return $this->hasMany(\App\Models\Broadcast\BroadcastLog::class);
    }
}

