<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Survey extends Model
{
    protected $fillable = [
        'order_id',
        'assigned_to',
        'scheduled_at',
        'location',
        'status',
        'findings',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'scheduled_at' => 'datetime',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'assigned_to'
        );
    }

    public function workEstimate(): HasOne
    {
        return $this->hasOne(
            WorkEstimate::class
        );
    }
}