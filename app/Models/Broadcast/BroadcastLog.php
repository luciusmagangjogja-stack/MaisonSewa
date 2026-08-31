<?php

declare(strict_types=1);

namespace App\Models\Broadcast;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class BroadcastLog extends Model
{
    use HasFactory;

    protected $table = 'broadcast_logs';

    protected $fillable = [
        'campaign_id',
        'recipient_type',
        'recipient_id',
        'phone',
        'rendered_message',
        'provider',
        'provider_message_id',
        'status',
        'error_message',
        'attempt_count',
        'sent_at',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'attempt_count' => 'integer',
    ];

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(BroadcastCampaign::class, 'campaign_id');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function recipient(): MorphTo
    {
        return $this->morphTo();
    }
}

