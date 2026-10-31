<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Survey extends Model
{
    protected $fillable = [
        'sps_number',
        'order_id',
        'requested_by',
        'assigned_to',
        'scheduled_at',
        'location',
        'status',
        'findings',
        'notes',
        'submitted_to_direktur_at',
        'approved_by',
        'approved_at',
        'rejection_notes',
    ];

    protected function casts(): array
    {
        return [
            'scheduled_at' => 'datetime',
            'submitted_to_direktur_at' => 'datetime',
            'approved_at' => 'datetime',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function project(): HasOne
    {
        return $this->hasOne(Project::class);
    }
}
