<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Hasil Survei - {{ $survey->sps_number ?: $survey->id }}</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 10pt; color: #1f2937; line-height: 1.45; margin: 20px 25px; }
        .header { border-bottom: 2px solid #1e3a8a; padding-bottom: 8px; margin-bottom: 15px; }
        .company-name { font-size: 15pt; font-weight: bold; color: #1e3a8a; text-transform: uppercase; }
        .company-sub { font-size: 8.5pt; color: #6b7280; }
        .doc-title { text-align: center; font-size: 13pt; font-weight: bold; text-transform: uppercase; margin: 10px 0 2px; color: #111827; }
        .doc-sub { text-align: center; font-size: 9pt; color: #4b5563; margin-bottom: 15px; font-weight: bold; }
        table.meta { width: 100%; border-collapse: collapse; margin-bottom: 12px; }
        table.meta td { padding: 5px 8px; vertical-align: top; font-size: 9.5pt; }
        table.meta td.label { width: 28%; font-weight: bold; color: #374151; background-color: #f9fafb; border: 1px solid #e5e7eb; }
        table.meta td.value { width: 72%; border: 1px solid #e5e7eb; }
        .section-title { font-size: 10.5pt; font-weight: bold; color: #1e3a8a; border-bottom: 1px solid #e5e7eb; padding-bottom: 3px; margin-top: 14px; margin-bottom: 6px; text-transform: uppercase; }
        .box { border: 1px solid #e5e7eb; padding: 8px 10px; border-radius: 4px; background: #fafafa; font-size: 9.5pt; margin-bottom: 10px; }
        table.photos { width: 100%; border-collapse: collapse; margin-top: 8px; }
        table.photos td { width: 33.33%; padding: 4px; text-align: center; vertical-align: top; }
        .photo-img { max-width: 100%; height: 110px; object-fit: cover; border: 1px solid #d1d5db; border-radius: 3px; }
        .photo-caption { font-size: 8pt; color: #6b7280; margin-top: 2px; }
        .signature-table { width: 100%; border-collapse: collapse; margin-top: 25px; page-break-inside: avoid; }
        .signature-table td { width: 50%; vertical-align: top; text-align: center; font-size: 9.5pt; padding: 10px; }
        .sig-box { min-height: 80px; display: block; margin: 5px auto; }
        .sig-img { max-height: 75px; max-width: 180px; }
        .statement { font-size: 8pt; color: #4b5563; font-style: italic; margin-top: 4px; }
    </style>
</head>
<body>
    <div class="header">
        <table style="width: 100%;">
            <tr>
                <td>
                    <div class="company-name">SIMOP</div>
                    <div class="company-sub">Sistem Informasi Manajemen Operasional Perusahaan</div>
                </td>
                <td style="text-align: right; font-size: 8.5pt; color: #6b7280;">
                    Nomor SPS: <strong>{{ $survey->sps_number }}</strong><br>
                    Tanggal Terbit: {{ $survey->approved_at?->format('d/m/Y') }}
                </td>
            </tr>
        </table>
    </div>

    <div class="doc-title">Berita Acara & Laporan Hasil Survei Lapangan</div>
    <div class="doc-sub">No. Dokumen: {{ $survey->sps_number }} &bull; Tanggal Pelaksanaan: {{ $survey->executed_on?->format('d F Y') ?? '-' }}</div>

    <div class="section-title">1. Data Pelanggan & Petugas Survei</div>
    <table class="meta">
        <tr>
            <td class="label">Nama Pelanggan</td>
            <td class="value"><strong>{{ $survey->customer_display_name }}</strong></td>
        </tr>
        <tr>
            <td class="label">Nomor Pesanan / Kegiatan</td>
            <td class="value">{{ $survey->order?->order_number ?? '-' }} @if($survey->order?->event_name) ({{ $survey->order->event_name }}) @endif</td>
        </tr>
        <tr>
            <td class="label">Alamat Lokasi Survei</td>
            <td class="value">{{ $survey->location }}</td>
        </tr>
        <tr>
            <td class="label">Jenis Survei</td>
            <td class="value">{{ $survey->survey_type ?: 'Survei Lapangan' }}</td>
        </tr>
        <tr>
            <td class="label">Nama Petugas Survei</td>
            <td class="value"><strong>{{ $survey->assignedUser?->name ?? '-' }}</strong></td>
        </tr>
        <tr>
            <td class="label">Disetujui Oleh (Direktur)</td>
            <td class="value">{{ $survey->approver?->name ?? 'Direktur' }} (Pada {{ $survey->approved_at?->format('d M Y, H:i') }} WIB)</td>
        </tr>
    </table>

    <div class="section-title">2. Hasil Pemeriksaan di Lapangan</div>
    <div class="box">
        <strong>Temuan & Pengukuran Fisik Lapangan:</strong><br>
        {!! nl2br(e($survey->findings ?: '-')) !!}
    </div>

    <div class="box">
        <strong>Kondisi Lokasi (Akses, Daya Listrik, Area Venue):</strong><br>
        {!! nl2br(e($survey->location_condition ?: '-')) !!}
    </div>

    @if ($survey->result_notes)
        <div class="box">
            <strong>Catatan Tambahan Hasil Survei:</strong><br>
            {!! nl2br(e($survey->result_notes)) !!}
        </div>
    @endif

    <div class="box" style="background-color: #eff6ff; border-color: #bfdbfe;">
        <strong style="color: #1e3a8a;">Kesimpulan & Rekomendasi Hasil Survei:</strong><br>
        {!! nl2br(e($survey->conclusion ?: '-')) !!}
    </div>

    @if ($photos && $photos->isNotEmpty())
        <div class="section-title">3. Dokumentasi Foto Lapangan</div>
        <table class="photos">
            @foreach ($photos->chunk(3) as $row)
                <tr>
                    @foreach ($row as $p)
                        <td>
                            <img src="{{ $p['src'] }}" class="photo-img"><br>
                            <span class="photo-caption">{{ $p['caption'] }}</span>
                        </td>
                    @endforeach
                    @for ($i = count($row); $i < 3; $i++)
                        <td></td>
                    @endfor
                </tr>
            @endforeach
        </table>
    @endif

    <div class="section-title">{{ $photos && $photos->isNotEmpty() ? '4.' : '3.' }} Bukti & Tanda Tangan Pelaksanaan Survei</div>
    <table class="signature-table">
        <tr>
            <td>
                Pelaksana Lapangan,<br>
                <strong>Petugas Survei</strong>
                <div class="sig-box" style="height: 65px;"></div>
                <u><strong>{{ $survey->assignedUser?->name ?? 'Petugas Survei' }}</strong></u><br>
                <span style="font-size: 8pt; color: #6b7280;">Waktu Pelaksanaan: {{ $survey->executed_on?->format('d/m/Y') }}</span>
            </td>
            <td>
                Menyetujui Hasil Survei,<br>
                <strong>Pihak Pelanggan / Perwakilan</strong>
                <div class="sig-box">
                    @if ($signature)
                        <img src="{{ $signature }}" class="sig-img">
                    @else
                        <div style="height: 65px;"></div>
                    @endif
                </div>
                <u><strong>{{ $survey->signer_name ?: $survey->customer_display_name }}</strong></u><br>
                @if ($survey->signer_identity)
                    <span style="font-size: 8.5pt; color: #4b5563;">ID/Telp: {{ $survey->signer_identity }}</span><br>
                @endif
                <span style="font-size: 8pt; color: #6b7280;">Ditandatangani secara digital pada: {{ $survey->signed_at?->format('d/m/Y H:i:s') }} WIB</span>
                <div class="statement">"Pelanggan menyatakan survei telah dilaksanakan dan menyetujui seluruh hasil pemeriksaan ini."</div>
            </td>
        </tr>
    </table>
</body>
</html>
