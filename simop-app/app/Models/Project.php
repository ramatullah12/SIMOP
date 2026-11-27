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

    public function scopeAccessibleByOfficer($query, int $userId)
    {
        return $query->where(function ($q) use ($userId) {
            $q->whereNull('assigned_to')
                ->orWhere('assigned_to', $userId)
                ->orWhereHas('survey', fn ($s) => $s->where('assigned_to', $userId))
                ->orWhereHas('planning.production', fn ($p) => $p->where('assigned_to', $userId));
        });
    }

    public function isAccessibleByOfficer(int $userId): bool
    {
        if ($this->assigned_to === null || $this->assigned_to === $userId) {
            return true;
        }

        if ($this->survey?->assigned_to === $userId) {
            return true;
        }

        if ($this->planning?->production?->assigned_to === $userId) {
            return true;
        }

        return false;
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'planning' => 'Perencanaan',
            'in_progress', 'ongoing' => 'Sedang Dikerjakan',
            'completed' => 'Selesai',
            'cancelled' => 'Dibatalkan',
            default => strtoupper(str_replace('_', ' ', (string) $this->status)),
        };
    }

    /**
     * Hitung persentase kemajuan proyek secara otomatis berdasarkan tahapan proses.
     */
    public function calculateAutoProgress(): int
    {
        if ($this->status === 'completed') {
            return 100;
        }

        if ($this->status === 'cancelled') {
            return 0;
        }

        $progress = 25; // Tahap 1: Proyek dibuat

        // Tahap 2: Alokasi stok barang (+25%)
        if ($this->projectItems()->exists()) {
            $progress += 25;
        }

        // Tahap 3: Dokumentasi lapangan (+25%)
        if ($this->fieldDocumentations()->exists()) {
            $progress += 25;
        }

        return min(75, $progress);
    }

    /**
     * Hitung dan simpan persentase kemajuan otomatis ke database.
     */
    public function syncAutoProgress(): int
    {
        $progress = $this->calculateAutoProgress();

        if ((int) $this->progress !== $progress) {
            $this->update(['progress' => $progress]);
        }

        return $progress;
    }

    /**
     * Rincian tahapan progres otomatis untuk ditampilkan di detail proyek.
     */
    public function getAutoProgressStepsAttribute(): array
    {
        $hasItems = $this->relationLoaded('projectItems') ? $this->projectItems->isNotEmpty() : $this->projectItems()->exists();
        $hasDocs = $this->relationLoaded('fieldDocumentations') ? $this->fieldDocumentations->isNotEmpty() : $this->fieldDocumentations()->exists();
        $isCompleted = $this->status === 'completed';

        return [
            [
                'label' => 'Proyek Dibuat',
                'weight' => '25%',
                'done' => $this->status !== 'cancelled',
            ],
            [
                'label' => 'Alokasi Stok Barang',
                'weight' => '+25%',
                'done' => $isCompleted || $hasItems,
            ],
            [
                'label' => 'Dokumentasi Lapangan',
                'weight' => '+25%',
                'done' => $isCompleted || $hasDocs,
            ],
            [
                'label' => 'Proyek Selesai & Stok Kembali',
                'weight' => '100%',
                'done' => $isCompleted,
            ],
        ];
    }
}
