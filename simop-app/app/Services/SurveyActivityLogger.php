<?php

namespace App\Services;

use App\Models\Survey;
use App\Models\SurveyActivity;
use App\Models\User;

class SurveyActivityLogger
{
    public static function log(
        Survey $survey,
        string $action,
        string $description,
        ?string $statusBefore = null,
        ?string $statusAfter = null,
        array $changes = [],
        ?User $user = null
    ): SurveyActivity {
        $user ??= auth()->user();

        return SurveyActivity::create([
            'survey_id' => $survey->id,
            'user_id' => $user?->id,
            'user_name' => $user?->name ?? 'Sistem',
            'user_role' => $user?->role?->name ?? 'Sistem',
            'action' => $action,
            'description' => $description,
            'status_before' => $statusBefore,
            'status_after' => $statusAfter,
            'changes' => $changes ?: null,
            'created_at' => now(),
        ]);
    }

    /** Label singkat untuk jenis aktivitas. */
    public static function actionLabel(string $action): string
    {
        return [
            'dibuat' => 'Pengajuan Dibuat',
            'diperbarui' => 'Pengajuan Diperbaiki',
            'dikirim_direktur' => 'Dikirim ke Direktur',
            'disetujui' => 'Disetujui Direktur',
            'ditolak' => 'Ditolak Direktur',
            'sps_terbit' => 'SPS Diterbitkan',
            'survei_dimulai' => 'Survei Dimulai',
            'pemeriksaan_selesai' => 'Pemeriksaan Lapangan Selesai',
            'hasil_disimpan' => 'Hasil Survei Diisi',
            'foto_diunggah' => 'Foto Lokasi Diunggah',
            'foto_dihapus' => 'Foto Lokasi Dihapus',
            'ditandatangani' => 'Tanda Tangan Pelanggan',
            'selesai' => 'Survei Selesai Dikirim',
            'koreksi_dibuka' => 'Koreksi Hasil Dibuka',
        ][$action] ?? ucwords(str_replace('_', ' ', $action));
    }
}
