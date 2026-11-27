<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Invoice {{ $invoiceNumber }}</title>
    <style>
        @page {
            margin: 28px 36px;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 10pt;
            color: #1e293b;
            line-height: 1.45;
            margin: 0;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
            border-bottom: 2px solid #b91c1c;
            padding-bottom: 12px;
            margin-bottom: 20px;
        }
        .header-table td {
            vertical-align: middle;
            padding-bottom: 10px;
        }
        .brand-logo img {
            max-width: 145px;
            max-height: 68px;
        }
        .company-name {
            font-size: 14pt;
            font-weight: bold;
            color: #991b1b;
            letter-spacing: 0.3px;
        }
        .company-sub {
            font-size: 8pt;
            color: #475569;
            margin-top: 2px;
        }
        .invoice-title-box {
            text-align: right;
        }
        .invoice-title {
            font-size: 20pt;
            font-weight: bold;
            color: #0f172a;
            letter-spacing: 1px;
        }
        .invoice-no {
            font-size: 9.5pt;
            font-weight: bold;
            color: #1d4ed8;
            margin-top: 2px;
        }
        .status-badge {
            display: inline-block;
            margin-top: 6px;
            padding: 3px 10px;
            border-radius: 12px;
            font-size: 8pt;
            font-weight: bold;
            text-transform: uppercase;
        }
        .status-done {
            background: #dcfce7;
            color: #166534;
            border: 1px solid #86efac;
        }
        .status-active {
            background: #dbeafe;
            color: #1e40af;
            border: 1px solid #93c5fd;
        }
        .status-rejected {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fca5a5;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 18px;
        }
        .info-table td {
            width: 50%;
            vertical-align: top;
            padding: 10px 12px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
        }
        .info-heading {
            font-size: 8pt;
            font-weight: bold;
            text-transform: uppercase;
            color: #64748b;
            letter-spacing: 0.5px;
            margin-bottom: 6px;
        }
        .info-row {
            font-size: 9.2pt;
            margin-bottom: 3px;
        }

        .section-title {
            font-size: 10pt;
            font-weight: bold;
            text-transform: uppercase;
            color: #0f172a;
            border-left: 4px solid #b91c1c;
            padding-left: 8px;
            margin: 18px 0 8px 0;
        }

        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
        }
        table.data-table th {
            background: #1e293b;
            color: #ffffff;
            text-align: left;
            padding: 7px 10px;
            font-size: 8.5pt;
            text-transform: uppercase;
        }
        table.data-table td {
            padding: 8px 10px;
            border: 1px solid #cbd5e1;
            font-size: 9pt;
            vertical-align: top;
        }
        table.data-table tr:nth-child(even) td {
            background: #f8fafc;
        }

        .summary-box {
            border: 1px solid #cbd5e1;
            background: #f8fafc;
            padding: 10px 12px;
            margin-top: 8px;
            font-size: 9pt;
        }

        .footer-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 32px;
        }
        .footer-table td {
            width: 50%;
            vertical-align: top;
            font-size: 9pt;
        }
        .sign-box {
            text-align: center;
        }
        .sign-space {
            height: 60px;
        }
        .doc-note {
            margin-top: 22px;
            padding-top: 8px;
            border-top: 1px dashed #cbd5e1;
            font-size: 7.8pt;
            color: #64748b;
            text-align: center;
        }
    </style>
</head>
<body>
    @php
        $isDone = $order->status === 'selesai' || $order->project?->status === 'completed';
        $isRejected = $order->status === 'ditolak';
        $statusClass = $isDone ? 'status-done' : ($isRejected ? 'status-rejected' : 'status-active');
        $statusText = $isDone ? 'SELESAI' : strtoupper(str_replace('_', ' ', $order->status));
        $projectItems = $order->project?->projectItems ?? collect();
    @endphp

    {{-- HEADER --}}
    <table class="header-table">
        <tr>
            <td style="width: 55%;">
                @if (! empty($logoDataUri))
                    <div class="brand-logo" style="margin-bottom: 4px;">
                        <img src="{{ $logoDataUri }}" alt="PT. Solusi Media Group">
                    </div>
                @endif
                <div class="company-name">PT. SOLUSI MEDIA GROUP</div>
                <div class="company-sub">
                    CV. Media Solution &bull; Advertising, Event Organizer &amp; Event Production Service<br>
                    Jl. AKBP Cek Agus No. 12 RT 06 RW 01 Kec. IT II Palembang<br>
                    Telp: 0711-712636 / 081268026000
                </div>
            </td>
            <td style="width: 45%;" class="invoice-title-box">
                <div class="invoice-title">INVOICE</div>
                <div class="invoice-no">{{ $invoiceNumber }}</div>
                <div style="font-size: 8.5pt; color: #475569; margin-top: 2px;">
                    Tanggal Cetak: {{ now()->translatedFormat('d F Y') }}
                </div>
                <span class="status-badge {{ $statusClass }}">{{ $statusText }}</span>
            </td>
        </tr>
    </table>

    {{-- INFO PELANGGAN & PESANAN --}}
    <table class="info-table">
        <tr>
            <td>
                <div class="info-heading">Ditagihkan / Kepada Pelanggan</div>
                <div class="info-row"><strong>{{ $order->company_name }}</strong></div>
                <div class="info-row">PIC: {{ $order->contact_person }}</div>
                <div class="info-row">Telepon: {{ $order->phone ?: '-' }}</div>
                <div class="info-row">Email: {{ $order->email ?: ($order->client?->email ?? '-') }}</div>
            </td>
            <td>
                <div class="info-heading">Informasi Pesanan &amp; Proyek</div>
                <div class="info-row">No. Pesanan: <strong>{{ $order->order_number }}</strong></div>
                <div class="info-row">Tanggal Pesan: {{ $order->created_at?->translatedFormat('d F Y') ?? '-' }}</div>
                <div class="info-row">No. Survei (SPS): <strong>{{ $order->survey?->sps_number ?: '-' }}</strong></div>
                <div class="info-row">No. Proyek: <strong>{{ $order->project?->project_number ?: '-' }}</strong></div>
            </td>
        </tr>
    </table>

    {{-- RINCIAN PESANAN --}}
    <div class="section-title">Rincian Pesanan Kegiatan</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 28%;">Nama Kegiatan</th>
                <th style="width: 18%;">Tanggal Event</th>
                <th style="width: 24%;">Lokasi Kegiatan</th>
                <th style="width: 30%;">Deskripsi Kebutuhan</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>{{ $order->event_name }}</strong></td>
                <td>{{ $order->event_date ? $order->event_date->translatedFormat('d F Y') : 'Belum Ditentukan' }}</td>
                <td>{{ $order->event_location }}</td>
                <td>{!! nl2br(e($order->description)) !!}</td>
            </tr>
        </tbody>
    </table>

    {{-- RINCIAN PERLENGKAPAN / BARANG PROYEK --}}
    @if ($projectItems->isNotEmpty())
        <div class="section-title">Daftar Perlengkapan &amp; Barang Operasional</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 8%;">No</th>
                    <th style="width: 47%;">Nama Barang / Perlengkapan</th>
                    <th style="width: 20%;">Jumlah</th>
                    <th style="width: 25%;">Status Pengembalian</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($projectItems as $idx => $pi)
                    <tr>
                        <td>{{ $idx + 1 }}</td>
                        <td>
                            <strong>{{ $pi->item?->name ?? '-' }}</strong>
                            @if ($pi->item?->item_code)
                                <br><span style="font-size: 7.8pt; color: #64748b;">Kode: {{ $pi->item->item_code }}</span>
                            @endif
                        </td>
                        <td><strong>{{ $pi->quantity }} {{ $pi->item?->unit }}</strong></td>
                        <td>{{ $pi->status === 'returned' ? 'Sudah Kembali ke Gudang' : 'Digunakan di Proyek' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    {{-- RINGKASAN PELAKSANAAN --}}
    <div class="section-title">Ringkasan Status Pelaksanaan</div>
    <div class="summary-box">
        <table style="width: 100%; border-collapse: collapse; font-size: 9pt;">
            <tr>
                <td style="width: 28%; padding: 3px 0; color: #475569;">Status Survei Lapangan</td>
                <td style="width: 72%; padding: 3px 0;">: <strong>{{ $order->survey?->status_label ?? 'Belum Ada Survei' }}</strong>
                    @if ($order->survey?->completed_at)
                        (Selesai: {{ $order->survey->completed_at->translatedFormat('d F Y') }})
                    @endif
                </td>
            </tr>
            <tr>
                <td style="padding: 3px 0; color: #475569;">Status Proyek Operasional</td>
                <td style="padding: 3px 0;">: <strong>{{ $order->project?->status_label ?? 'Belum Dibuat' }}</strong>
                    @if ($order->project)
                        — Progres {{ (int) $order->project->progress }}%
                    @endif
                </td>
            </tr>
            @if ($order->estimated_budget)
                <tr>
                    <td style="padding: 3px 0; color: #475569;">Estimasi Nilai Pesanan</td>
                    <td style="padding: 3px 0;">: <strong>Rp {{ number_format((float) $order->estimated_budget, 0, ',', '.') }}</strong></td>
                </tr>
            @endif
            @if ($order->admin_notes)
                <tr>
                    <td style="padding: 3px 0; color: #475569;">Catatan Admin</td>
                    <td style="padding: 3px 0;">: {{ $order->admin_notes }}</td>
                </tr>
            @endif
        </table>
    </div>

    {{-- PENGESAHAN --}}
    <table class="footer-table">
        <tr>
            <td>
                <strong>Keterangan:</strong><br>
                <span style="font-size: 8.2pt; color: #475569;">
                    Dokumen ini merupakan bukti resmi riwayat pesanan dan pelaksanaan proyek dari sistem SIMOP PT. Solusi Media Group.
                </span>
            </td>
            <td class="sign-box">
                Palembang, {{ now()->translatedFormat('d F Y') }}<br>
                <strong>PT. Solusi Media Group</strong>
                <div class="sign-space"></div>
                <u><strong>Administrasi Operasional</strong></u>
            </td>
        </tr>
    </table>

    <div class="doc-note">
        Dicetak otomatis melalui SIMOP &bull; {{ $invoiceNumber }} &bull; {{ $order->company_name }}
    </div>
</body>
</html>
