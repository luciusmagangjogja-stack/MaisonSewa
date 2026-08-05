<?php

declare(strict_types=1);

namespace App\Models\Broadcast;

use App\Models\Branch;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BroadcastSchedule extends Model
{
    use HasFactory;

    protected $table = 'broadcast_schedules';

    protected $fillable = [
        'name',
        'template_id',
        'trigger_event',
        'frequency',
        'run_time',
        'run_day_of_week',
        'run_day_of_month',
        'run_month',
        'target_type',
        'target_filters',
        'provider',
        'is_active',
        'next_run_at',
        'last_run_at',
        'created_by',
        'branch_id',
    ];

    protected $casts = [
        'target_filters' => 'array',
        'run_time' => 'datetime:H:i:s',
        'next_run_at' => 'datetime',
        'last_run_at' => 'datetime',
        'is_active' => 'boolean',
        'run_day_of_week' => 'integer',
        'run_day_of_month' => 'integer',
        'run_month' => 'integer',
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
}

