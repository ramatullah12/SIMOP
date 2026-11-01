<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Perintah Survei - {{ $survey->sps_number }}</title>
    <style>
        body { font-family: 'Times New Roman', Times, serif; font-size: 12pt; color: #111827; line-height: 1.5; margin: 25px 35px; }
        .kop { text-align: center; border-bottom: 3px double #111827; padding-bottom: 8px; margin-bottom: 20px; }
        .kop-title { font-size: 16pt; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; color: #1e3a8a; }
        .kop-sub { font-size: 10pt; color: #4b5563; font-family: 'Helvetica Neue', Arial, sans-serif; }
        .surat-title { text-align: center; font-size: 14pt; font-weight: bold; text-transform: uppercase; text-decoration: underline; margin-top: 15px; }
        .surat-nomor { text-align: center; font-size: 11pt; margin-top: 2px; margin-bottom: 25px; }
        p { margin: 6px 0; text-align: justify; }
        table.detail { width: 100%; border-collapse: collapse; margin: 12px 0 16px 20px; }
        table.detail td { padding: 4px 6px; vertical-align: top; font-size: 11pt; }
        table.detail td.key { width: 25%; font-weight: bold; }
        table.detail td.separator { width: 3%; }
        table.detail td.val { width: 72%; }
        .penutup { margin-top: 15px; }
        .ttd-container { width: 100%; margin-top: 35px; }
        .ttd-box { width: 45%; margin-left: 55%; text-align: center; }
        .ttd-space { height: 75px; }
    </style>
</head>
<body>
    <div class="kop">
        <div class="kop-title">SIMOP OPERASIONAL EVENT & EXHIBITION</div>
        <div class="kop-sub">Sistem Informasi Manajemen Operasional Perusahaan</div>
        <div class="kop-sub">Jl. Operasional No. 88 &bull; Telp: (021) 555-0199 &bull; Email: operasional@simop.id</div>
    </div>

    <div class="surat-title">SURAT PERINTAH SURVEI (SPS)</div>
    <div class="surat-nomor">Nomor: {{ $survey->sps_number }}</div>

    <p>
        Yang bertanda tangan di bawah ini selaku Direktur Perusahaan, dengan ini memberikan perintah tugas kepada:
    </p>

    <table class="detail">
        <tr>
            <td class="key">Nama Petugas</td>
            <td class="separator">:</td>
            <td class="val"><strong>{{ $survey->assignedUser?->name ?? '-' }}</strong></td>
        </tr>
        <tr>
            <td class="key">Jabatan</td>
            <td class="separator">:</td>
            <td class="val">Petugas Lapangan / Petugas Survei Operasional</td>
        </tr>
        <tr>
            <td class="key">Kontak Petugas</td>
            <td class="separator">:</td>
            <td class="val">{{ $survey->assignedUser?->email ?? '-' }}</td>
        </tr>
    </table>

    <p>
        Untuk melaksanakan tugas survei lapangan dan pemeriksaan teknis pada:
    </p>

    <table class="detail">
        <tr>
            <td class="key">Identitas Pelanggan</td>
            <td class="separator">:</td>
            <td class="val"><strong>{{ $survey->customer_display_name }}</strong></td>
        </tr>
        <tr>
            <td class="key">Nomor Pesanan</td>
            <td class="separator">:</td>
            <td class="val">{{ $survey->order?->order_number ?? '-' }} @if($survey->order?->event_name) (Kegiatan: {{ $survey->order->event_name }}) @endif</td>
        </tr>
        <tr>
            <td class="key">Alamat Lokasi Survei</td>
            <td class="separator">:</td>
            <td class="val">{{ $survey->location }}</td>
        </tr>
        <tr>
            <td class="key">Jenis Survei</td>
            <td class="separator">:</td>
            <td class="val">{{ $survey->survey_type ?: 'Survei Lapangan' }}</td>
        </tr>
        <tr>
            <td class="key">Jadwal Pelaksanaan</td>
            <td class="separator">:</td>
            <td class="val">{{ $survey->scheduled_at?->format('d F Y, H:i') ?? '-' }} WIB</td>
        </tr>
        <tr>
            <td class="key">Tujuan Pelaksanaan</td>
            <td class="separator">:</td>
            <td class="val">{{ $survey->purpose ?: '-' }}</td>
        </tr>
        @if ($survey->approval_notes)
            <tr>
                <td class="key">Instruksi Khusus</td>
                <td class="separator">:</td>
                <td class="val">{{ $survey->approval_notes }}</td>
            </tr>
        @endif
    </table>

    <p class="penutup">
        Petugas diwajibkan melakukan koordinasi dengan pihak pelanggan di lokasi, mengisi Form Hasil Survei secara lengkap, serta meminta tanda tangan digital pelanggan sebagai bukti sah pelaksanaan survei.
    </p>

    <p>
        Demikian Surat Perintah Survei ini diterbitkan untuk dilaksanakan dengan sebaik-baiknya dan penuh tanggung jawab.
    </p>

    <table class="ttd-container">
        <tr>
            <td></td>
            <td class="ttd-box">
                Diterbitkan pada: {{ ($survey->sps_issued_at ?? $survey->approved_at ?? now())->format('d F Y') }}<br>
                Disetujui oleh,<br>
                <strong>Direktur Perusahaan</strong>
                <div class="ttd-space"></div>
                <u><strong>{{ $survey->approver?->name ?? 'Direktur' }}</strong></u>
            </td>
        </tr>
    </table>
</body>
</html>

