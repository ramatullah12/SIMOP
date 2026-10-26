<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Project extends Model
{
    protected $fillable = [
        'work_estimate_id',
        'created_by',
        'assigned_to',
        'project_number',
        'title',
        'start_date',
        'deadline',
        'status',
        'progress',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'deadline' => 'date',
            'progress' => 'integer',
        ];
    }

    public function workEstimate(): BelongsTo
    {
        return $this->belongsTo(WorkEstimate::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function planning(): HasOne
    {
        return $this->hasOne(Planning::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }
}