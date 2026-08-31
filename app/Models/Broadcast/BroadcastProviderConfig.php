<?php

declare(strict_types=1);

namespace App\Models\Broadcast;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BroadcastProviderConfig extends Model
{
    use HasFactory;

    protected $table = 'broadcast_provider_configs';

    protected $fillable = [
        'provider_key',
        'label',
        'is_active',
        'is_default',
        'config',
        'session_path',
        'last_connected_at',
        'priority',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_default' => 'boolean',
        'config' => 'encrypted:array',
        'last_connected_at' => 'datetime',
    ];
}

