<?php

declare(strict_types=1);

namespace App\Models\Broadcast;

use App\Models\Branch;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class BroadcastTemplate extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'broadcast_templates';

    protected $fillable = [
        'name',
        'category',
        'content',
        'is_active',
        'branch_id',
        'created_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}

