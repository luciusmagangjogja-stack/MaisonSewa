<?php

declare(strict_types=1);

namespace App\Models\Broadcast;

use App\Models\Branch;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BroadcastCampaign extends Model
{
    use HasFactory;

    protected $table = 'broadcast_campaigns';

    protected $fillable = [
        'name',
        'type',
        'trigger_event',
        'template_id',
        'custom_message',
        'target_type',
        'target_filters',
        'provider',
        'status',
        'total_target',
        'total_success',
        'total_failed',
        'scheduled_at',
        'started_at',
        'completed_at',
        'created_by',
        'branch_id',
    ];

    protected $casts = [
        'target_filters' => 'array',
        'scheduled_at' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function template(): BelongsTo
    {
        return $this->belongsTo(BroadcastTemplate::class, 'template_id');
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function logs(): HasMany
    {
        return $this->hasMany(BroadcastLog::class, 'campaign_id');
    }
}

