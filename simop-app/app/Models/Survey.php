<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Survey extends Model
{
    public const STATUS_DRAFT = 'draft';
    public const STATUS_WAITING_DIRECTOR = 'menunggu_persetujuan_direktur';
    public const STATUS_REJECTED = 'ditolak_direktur';
    public const STATUS_APPROVED = 'disetujui_direktur';
    public const STATUS_WAITING_EXECUTION = 'menunggu_pelaksanaan';
    public const STATUS_IN_PROGRESS = 'berlangsung';
    public const STATUS_WAITING_RESULT = 'menunggu_hasil';
    public const STATUS_DONE = 'selesai';

    /** Status yang sudah boleh dilihat Petugas Survei (setelah disetujui Direktur). */
    public const OFFICER_STATUSES = [
        self::STATUS_WAITING_EXECUTION,
        self::STATUS_IN_PROGRESS,
        self::STATUS_WAITING_RESULT,
        self::STATUS_DONE,
    ];

    public const SURVEY_TYPES = [
        'Survei Lokasi Kegiatan',
        'Pengukuran Area',
        'Pengecekan Akses & Bongkar Muat',
        'Pengecekan Kelistrikan',
        'Survei Kebutuhan Perlengkapan',
        'Lainnya',
    ];

    protected $fillable = [
        'sps_number',
        'order_id',
        'requested_by',
        'customer_name',
        'assigned_to',
        'scheduled_at',
        'location',
        'survey_type',
        'purpose',
        'status',
        'findings',
        'notes',
        'submitted_to_direktur_at',
        'approved_by',
        'approved_at',
        'rejection_notes',
        'rejected_by',
        'rejected_at',
        'approval_notes',
        'sps_issued_at',
        'started_at',
        'field_finished_at',
        'executed_on',
        'location_condition',
        'result_notes',
        'conclusion',
        'completed_at',
        'signature_path',
        'signer_name',
        'signer_identity',
        'signer_agreed',
        'signed_at',
    ];

    protected function casts(): array
    {
        return [
            'scheduled_at' => 'datetime',
            'submitted_to_direktur_at' => 'datetime',
            'approved_at' => 'datetime',
            'rejected_at' => 'datetime',
            'sps_issued_at' => 'datetime',
            'started_at' => 'datetime',
            'field_finished_at' => 'datetime',
            'executed_on' => 'date',
            'completed_at' => 'datetime',
            'signed_at' => 'datetime',
            'signer_agreed' => 'boolean',
        ];
    }

    public static function statusLabels(): array
    {
        return [
            self::STATUS_DRAFT => 'Draf Pengajuan',
            self::STATUS_WAITING_DIRECTOR => 'Menunggu Persetujuan Direktur',
            self::STATUS_REJECTED => 'Ditolak Direktur',
            self::STATUS_APPROVED => 'Disetujui Direktur',
            self::STATUS_WAITING_EXECUTION => 'Menunggu Pelaksanaan Survei',
            self::STATUS_IN_PROGRESS => 'Survei Sedang Berlangsung',
            self::STATUS_WAITING_RESULT => 'Menunggu Pengisian Hasil Survei',
            self::STATUS_DONE => 'Survei Selesai',
            // status lama, tetap dikenali
            'diajukan_client' => 'Draf Pengajuan',
            'dijadwalkan' => 'Menunggu Pelaksanaan Survei',
        ];
    }

    public static function labelFor(?string $status): string
    {
        return static::statusLabels()[$status] ?? ucwords(str_replace('_', ' ', (string) $status));
    }

    public function getStatusLabelAttribute(): string
    {
        return static::labelFor($this->status);
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_DRAFT, 'diajukan_client' => 'bg-gray-100 text-gray-700',
            self::STATUS_WAITING_DIRECTOR => 'bg-purple-100 text-purple-800',
            self::STATUS_REJECTED => 'bg-red-100 text-red-800',
            self::STATUS_APPROVED => 'bg-emerald-100 text-emerald-800',
            self::STATUS_WAITING_EXECUTION, 'dijadwalkan' => 'bg-blue-100 text-blue-800',
            self::STATUS_IN_PROGRESS => 'bg-indigo-100 text-indigo-800',
            self::STATUS_WAITING_RESULT => 'bg-amber-100 text-amber-800',
            self::STATUS_DONE => 'bg-green-100 text-green-800',
            default => 'bg-gray-100 text-gray-700',
        };
    }

    /** Admin masih boleh mengubah isi pengajuan. */
    public function isEditableByAdmin(): bool
    {
        return in_array($this->status, [self::STATUS_DRAFT, self::STATUS_REJECTED, 'diajukan_client'], true);
    }

    public function isSigned(): bool
    {
        return ! empty($this->signature_path) && $this->signed_at !== null;
    }

    public function getCustomerDisplayNameAttribute(): string
    {
        return $this->customer_name ?: ($this->order?->company_name ?? '-');
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

    public function rejecter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    public function project(): HasOne
    {
        return $this->hasOne(Project::class);
    }

    public function photos(): HasMany
    {
        return $this->hasMany(SurveyPhoto::class);
    }

    public function activities(): HasMany
    {
        return $this->hasMany(SurveyActivity::class)->orderByDesc('created_at')->orderByDesc('id');
    }
}
