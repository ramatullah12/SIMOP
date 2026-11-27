<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    protected $fillable = [
        'order_number',
        'client_id',
        'company_name',
        'contact_person',
        'phone',
        'email',
        'event_name',
        'event_location',
        'event_date',
        'description',
        'estimated_budget',
        'status',
        'admin_notes',
    ];

    protected function casts(): array
    {
        return [
            'event_date' => 'date',
            'estimated_budget' => 'decimal:2',
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function surveys(): HasMany
    {
        return $this->hasMany(Survey::class);
    }

    public function survey(): HasOne
    {
        return $this->hasOne(Survey::class)->latestOfMany();
    }

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    public function project(): HasOne
    {
        return $this->hasOne(Project::class)->latestOfMany();
    }

    /**
     * Tandai pesanan selesai sekaligus menyelesaikan seluruh data terkait
     * (proyek, pengembalian stok barang ke gudang, dan survei aktif) agar otomatis
     * terarsip di Riwayat Event.
     */
    public function markCompleted(): int
    {
        if ($this->status !== 'selesai') {
            $this->update(['status' => 'selesai']);
        }

        $returnedCount = 0;

        foreach ($this->projects()->get() as $project) {
            if ($project->status !== 'completed' || (int) $project->progress !== 100) {
                $project->update([
                    'status' => 'completed',
                    'progress' => 100,
                ]);
            }
            $returnedCount += $project->returnAllocatedStock();
        }

        $this->surveys()
            ->whereNotIn('status', [Survey::STATUS_DONE, Survey::STATUS_REJECTED])
            ->update([
                'status' => Survey::STATUS_DONE,
                'completed_at' => now(),
            ]);

        return $returnedCount;
    }
}
