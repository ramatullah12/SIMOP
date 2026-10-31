<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Collection;

class Project extends Model
{
    protected $fillable = [
        'order_id',
        'survey_id',
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

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function survey(): BelongsTo
    {
        return $this->belongsTo(Survey::class);
    }

    public function getWorkEstimateAttribute(): self
    {
        return $this;
    }

    public function getItemsAttribute(): Collection
    {
        return collect();
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

    public function projectItems(): HasMany
    {
        return $this->hasMany(ProjectItem::class);
    }

    public function fieldDocumentations(): HasMany
    {
        return $this->hasMany(FieldDocumentation::class);
    }

    public function rating(): HasOne
    {
        return $this->hasOne(Rating::class);
    }

    public function returnAllocatedStock(): int
    {
        return $this->projectItems()
            ->where('status', 'allocated')
            ->update([
                'status' => 'returned',
                'returned_at' => now(),
            ]);
    }
}
