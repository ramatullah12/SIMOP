<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Planning extends Model
{
    protected $fillable = [
        'project_id',
        'created_by',
        'planning_number',
        'title',
        'description',
        'planned_start_date',
        'planned_end_date',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'planned_start_date' => 'date',
            'planned_end_date' => 'date',
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function production(): HasOne
    {
        return $this->hasOne(Production::class);
    }
}