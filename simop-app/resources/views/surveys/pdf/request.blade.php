<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Form Pengajuan Survei - {{ $survey->id }}</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 11pt; color: #1f2937; line-height: 1.4; margin: 20px 25px; }
        .header { border-bottom: 2px solid #1e3a8a; padding-bottom: 10px; margin-bottom: 18px; }
        .company-name { font-size: 16pt; font-weight: bold; color: #1e3a8a; text-transform: uppercase; letter-spacing: 0.5px; }
        .company-sub { font-size: 9pt; color: #6b7280; }
        .doc-title { text-align: center; font-size: 13pt; font-weight: bold; text-transform: uppercase; margin: 15px 0 5px; color: #111827; }
        .doc-sub { text-align: center; font-size: 9pt; color: #6b7280; margin-bottom: 20px; }
        table.meta { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
        table.meta td { padding: 6px 8px; vertical-align: top; font-size: 10pt; }
        table.meta td.label { width: 28%; font-weight: bold; color: #374151; background-color: #f9fafb; border: 1px solid #e5e7eb; }
        table.meta td.value { width: 72%; border: 1px solid #e5e7eb; }
        .section-title { font-size: 11pt; font-weight: bold; color: #1e3a8a; border-bottom: 1px solid #e5e7eb; padding-bottom: 4px; margin-top: 16px; margin-bottom: 8px; text-transform: uppercase; }
        .box { border: 1px solid #e5e7eb; padding: 10px 12px; border-radius: 4px; background: #fafafa; font-size: 10pt; margin-bottom: 12px; }
        .signatures { margin-top: 30px; width: 100%; }
        .signatures td { width: 50%; text-align: center; vertical-align: top; font-size: 10pt; padding: 10px; }
        .sig-space { height: 60px; }
    </style>
</head>
<body>
    <div class="header">
        <table style="width: 100%;">
            <tr>
                <td>
                    <div class="company-name">SIMOP</div>
                    <div class="company-sub">Sistem Informasi Manajemen Operasional</div>
                </td>
                <td style="text-align: right; font-size: 9pt; color: #6b7280;">
                    Tanggal Cetak: {{ now()->format('d/m/Y H:i') }}
                </td>
            </tr>
        </table>
    </div>

    <div class="doc-title">Formulir Pengajuan Survei Lapangan</div>
    <div class="doc-sub">No. Registrasi: SRV-{{ str_pad($survey->id, 5, '0', STR_PAD_LEFT) }} &bull; Status: {{ strtoupper(str_replace('_', ' ', $survey->status)) }}</div>

    <div class="section-title">1. Data Pelanggan & Pesanan</div>
    <table class="meta">
        <tr>
            <td class="label">Nama Pelanggan</td>
            <td class="value"><strong>{{ $survey->customer_display_name }}</strong></td>
        </tr>
        <tr>
            <td class="label">Nomor Pesanan</td>
            <td class="value">{{ $survey->order?->order_number ?? '-' }} @if($survey->order?->event_name) ({{ $survey->order->event_name }}) @endif</td>
        </tr>
        <tr>
            <td class="label">Alamat Lokasi Survei</td>
            <td class="value">{{ $survey->location }}</td>
        </tr>
        <tr>
            <td class="label">Jenis Kebutuhan Survei</td>
            <td class="value">{{ $survey->survey_type ?: '-' }}</td>
        </tr>
    </table>

    <div class="section-title">2. Tujuan & Keterangan Survei</div>
    <div class="box">
        <strong>Tujuan Pelaksanaan Survei:</strong><br>
        {!! nl2br(e($survey->purpose ?: '-')) !!}
    </div>

    @if ($survey->notes)
        <div class="box">
            <strong>Catatan Pengantar Admin:</strong><br>
            {!! nl2br(e($survey->notes)) !!}
        </div>
    @endif

    <div class="section-title">3. Rencana Penugasan & Pelaksanaan</div>
    <table class="meta">
        <tr>
            <td class="label">Petugas Lapangan Diajukan</td>
            <td class="value">{{ $survey->assignedUser?->name ?? 'Belum ditentukan' }}</td>
        </tr>
        <tr>
            <td class="label">Jadwal Rencana Survei</td>
            <td class="value">{{ $survey->scheduled_at?->format('d F Y, H:i') ?? 'Belum dijadwalkan' }} WIB</td>
        </tr>
    </table>

    <div class="section-title">4. Keputusan Direktur</div>
    <table class="meta">
        <tr>
            <td class="label">Status Keputusan</td>
            <td class="value">
                @if ($survey->approved_at)
                    <strong style="color: #065f46;">DISETUJUI (Surat Perintah Survei Terbit: {{ $survey->sps_number }})</strong>
                @elseif ($survey->rejected_at)
                    <strong style="color: #991b1b;">DITOLAK / DIKEMBALIKAN UNTUK PERBAIKAN</strong>
                @else
                    <span style="color: #6b7280;">Menunggu Keputusan Direktur</span>
                @endif
            </td>
        </tr>
        @if ($survey->approved_at)
            <tr>
                <td class="label">Disetujui Oleh</td>
                <td class="value">{{ $survey->approver?->name ?? 'Direktur' }} ({{ $survey->approved_at->format('d/m/Y H:i') }} WIB)</td>
            </tr>
            @if ($survey->approval_notes)
                <tr>
                    <td class="label">Instruksi Direktur</td>
                    <td class="value">{!! nl2br(e($survey->approval_notes)) !!}</td>
                </tr>
            @endif
        @endif
        @if ($survey->rejected_at)
            <tr>
                <td class="label">Ditolak Oleh</td>
                <td class="value">{{ $survey->rejecter?->name ?? 'Direktur' }} ({{ $survey->rejected_at->format('d/m/Y H:i') }} WIB)</td>
            </tr>
            <tr>
                <td class="label">Alasan Penolakan</td>
                <td class="value" style="color: #991b1b;">{!! nl2br(e($survey->rejection_notes)) !!}</td>
            </tr>
        @endif
    </table>

    <table class="signatures">
        <tr>
            <td>
                Diajukan oleh,<br>
                <strong>Admin Operasional</strong>
                <div class="sig-space"></div>
                <u>{{ $survey->requester?->name ?? 'Admin SIMOP' }}</u>
            </td>
            <td>
                Mengetahui & Memutuskan,<br>
                <strong>Direktur</strong>
                <div class="sig-space"></div>
                <u>{{ $survey->approver?->name ?? $survey->rejecter?->name ?? '( Direktur Perusahaan )' }}</u>
            </td>
        </tr>
    </table>
</body>
</html>

