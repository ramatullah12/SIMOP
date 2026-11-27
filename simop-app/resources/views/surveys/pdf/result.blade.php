<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Survey Form - {{ $survey->sps_number ?: '#' . $survey->id }}</title>
    <style>
        @page {
            margin: 18px 22px;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 9pt;
            color: #111111;
            margin: 0;
            line-height: 1.35;
        }
        .outer-frame {
            border: 1.8px solid #111111;
            width: 100%;
            box-sizing: border-box;
        }

        /* HEADER SECTION */
        table.header-grid {
            width: 100%;
            border-collapse: collapse;
        }
        table.header-grid > tbody > tr > td {
            vertical-align: top;
            padding: 0;
        }
        td.header-left {
            width: 46%;
            padding: 8px 10px 8px 10px;
        }
        td.header-right {
            width: 54%;
            padding: 0;
        }

        /* Left Header Fields */
        table.left-top {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 6px;
        }
        .form-title {
            font-size: 14pt;
            font-weight: bold;
            text-decoration: underline;
            letter-spacing: 0.4px;
            padding-left: 14px;
        }
        .date-label {
            text-align: right;
            font-weight: bold;
            font-size: 8.5pt;
            padding-right: 5px;
            white-space: nowrap;
        }
        .date-box {
            border: 1px solid #111111;
            padding: 5px 8px;
            width: 135px;
            height: 22px;
            font-size: 9pt;
            font-weight: bold;
            vertical-align: middle;
        }

        table.cust-fields {
            width: 100%;
            border-collapse: collapse;
        }
        table.cust-fields td {
            padding: 3px 0;
            vertical-align: middle;
        }
        table.cust-fields td.lbl {
            width: 22%;
            font-size: 8.2pt;
            font-weight: bold;
            line-height: 1.15;
        }
        table.cust-fields td.col {
            width: 4%;
            text-align: center;
            font-weight: bold;
        }
        table.cust-fields td.inp {
            width: 74%;
        }
        .field-box {
            border: 1px solid #111111;
            padding: 4px 7px;
            min-height: 16px;
            font-size: 8.8pt;
            background: #ffffff;
        }

        /* Right Header Company Box */
        table.company-box {
            width: 100%;
            border-collapse: collapse;
            border-left: 1px solid #111111;
            border-bottom: 1px solid #111111;
        }
        table.company-box td {
            padding: 5px 8px;
            vertical-align: middle;
        }
        .logo-cell {
            width: 52%;
            text-align: center;
            border-right: 1px solid #111111;
        }
        .logo-cell img {
            max-width: 195px;
            max-height: 54px;
        }
        .services-cell {
            width: 48%;
            text-align: center;
            font-size: 7.8pt;
            font-weight: bold;
            line-height: 1.35;
        }
        .address-row td {
            border-top: 1px solid #111111;
            text-align: center;
            font-size: 7.8pt;
            font-weight: bold;
            padding: 4px 6px;
        }

        /* Right Meta Fields (RFQ NO, SALES, DATELINE, ORDER CODE) */
        table.meta-fields {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
        }
        table.meta-fields td {
            padding: 3px 4px;
            vertical-align: middle;
            font-size: 8.2pt;
            font-weight: bold;
        }
        .meta-box {
            border: 1px solid #111111;
            padding: 4px 6px;
            min-height: 16px;
            font-weight: normal;
            font-size: 8.6pt;
            background: #ffffff;
        }
        .meta-box-sm {
            border: 1px solid #111111;
            padding: 4px 6px;
            min-height: 16px;
            width: 34px;
            text-align: center;
            font-weight: bold;
            font-size: 8.6pt;
            display: inline-block;
        }

        /* MAIN BODY: DATA SURVEY & ORDER REQUEST SPESIFICATION */
        table.body-grid {
            width: 100%;
            border-collapse: collapse;
            border-top: 1.5px solid #111111;
        }
        table.body-grid td.col-left {
            width: 50%;
            border-right: 1px solid #111111;
            vertical-align: top;
            padding: 0;
        }
        table.body-grid td.col-right {
            width: 50%;
            vertical-align: top;
            padding: 0;
        }
        .panel-header {
            font-size: 8.8pt;
            font-weight: bold;
            text-transform: uppercase;
            padding: 4px 8px;
            border-bottom: 1px solid #111111;
            background: #ffffff;
        }
        .data-survey-content {
            padding: 10px 12px;
            height: 415px;
            font-size: 8.8pt;
        }
        .order-spec-content {
            padding: 10px 12px;
            height: 292px;
            font-size: 8.8pt;
        }
        .item-block {
            margin-bottom: 9px;
        }
        .item-title {
            font-weight: bold;
            font-size: 8.2pt;
            text-transform: uppercase;
            color: #1f2937;
            margin-bottom: 2px;
        }
        .item-text {
            font-size: 8.8pt;
            color: #111111;
        }

        /* SIGNATURE AREA (BOTTOM RIGHT) */
        table.sig-grid {
            width: 100%;
            border-collapse: collapse;
            border-top: 1px solid #111111;
        }
        table.sig-grid th {
            width: 50%;
            font-size: 8.5pt;
            font-weight: bold;
            text-align: center;
            padding: 4px 6px;
            border-bottom: 1px solid #111111;
        }
        table.sig-grid th:first-child,
        table.sig-grid td:first-child {
            border-right: 1px solid #111111;
        }
        table.sig-grid td {
            width: 50%;
            height: 98px;
            text-align: center;
            vertical-align: bottom;
            padding: 6px 8px;
        }
        .sig-img-wrap {
            height: 62px;
            text-align: center;
            margin-bottom: 2px;
        }
        .sig-img {
            max-height: 60px;
            max-width: 165px;
        }
        .sig-name {
            font-size: 8.5pt;
            font-weight: bold;
        }
        .sig-time {
            font-size: 7pt;
            color: #4b5563;
            margin-top: 1px;
        }

        /* OPTIONAL PAGE 2: PHOTOS */
        .page-break {
            page-break-before: always;
        }
        table.photos-grid {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        table.photos-grid td {
            width: 33.33%;
            text-align: center;
            vertical-align: top;
            padding: 6px;
            border: 1px solid #d1d5db;
        }
        table.photos-grid img {
            max-width: 100%;
            max-height: 155px;
        }
        .photo-cap {
            font-size: 8pt;
            color: #374151;
            margin-top: 4px;
        }
    </style>
</head>
<body>
    @php
        $surveyDate = ($survey->executed_on ?? $survey->completed_at ?? $survey->created_at)?->format('d / m / Y') ?? '-';
        $customerName = $survey->customer_display_name ?: '-';
        $customerAddress = $survey->location ?: ($survey->order?->event_location ?? '-');
        $customerPhone = $survey->signer_identity ?: ($survey->order?->phone ?? '-');
        $rfqNo = $survey->sps_number ?: ('SRV-' . $survey->id);
        $salesName = $survey->requester?->name ?? 'Admin';
        $orderCode = $survey->order?->order_number ?? '-';

        $datelineDays = '-';
        if ($survey->order?->event_date) {
            $baseDate = $survey->executed_on ?? $survey->created_at ?? now();
            $diffDays = (int) $baseDate->copy()->startOfDay()->diffInDays($survey->order->event_date->copy()->startOfDay(), false);
            $datelineDays = $diffDays >= 0 ? (string) $diffDays : '0';
        }
    @endphp

    <div class="outer-frame">
        {{-- BAGIAN ATAS --}}
        <table class="header-grid">
            <tr>
                {{-- KIRI ATAS --}}
                <td class="header-left">
                    <table class="left-top">
                        <tr>
                            <td class="form-title">SURVEY FORM</td>
                            <td class="date-label">DATE :</td>
                            <td class="date-box">{{ $surveyDate }}</td>
                        </tr>
                    </table>

                    <table class="cust-fields">
                        <tr>
                            <td class="lbl">CUSTOMER<br>NAME</td>
                            <td class="col">:</td>
                            <td class="inp"><div class="field-box">{{ $customerName }}</div></td>
                        </tr>
                        <tr>
                            <td class="lbl">ADDRESS</td>
                            <td class="col">:</td>
                            <td class="inp"><div class="field-box">{{ $customerAddress }}</div></td>
                        </tr>
                        <tr>
                            <td class="lbl">PHONE</td>
                            <td class="col">:</td>
                            <td class="inp"><div class="field-box">{{ $customerPhone }}</div></td>
                        </tr>
                    </table>
                </td>

                {{-- KANAN ATAS --}}
                <td class="header-right">
                    <table class="company-box">
                        <tr>
                            <td class="logo-cell">
                                @if (! empty($logoDataUri))
                                    <img src="{{ $logoDataUri }}" alt="Media Solution">
                                @else
                                    <div style="line-height: 1.15;">
                                        <span style="font-size: 15pt; font-weight: bold; color: #3898d4;">Media</span><span style="font-size: 15pt; font-weight: bold; color: #e88924;">Solution</span>
                                        <div style="font-size: 7.5pt; color: #4b5563;">All Media Design &amp; Working</div>
                                    </div>
                                @endif
                            </td>
                            <td class="services-cell">
                                ADVERTISING, EVENT ORGANIZER,<br>
                                PERCETAKAN, INTERIOR DESIGN &amp;<br>
                                FABRICATION, GENERAL CONTRACTOR
                            </td>
                        </tr>
                        <tr class="address-row">
                            <td colspan="2">
                                JL. AKBP CEK AGUS NO.12 PALEMBANG, TELP : 0711-712535 / 0821-8664-1111
                            </td>
                        </tr>
                    </table>

                    <table class="meta-fields">
                        <tr>
                            <td style="width: 15%; padding-left: 8px; white-space: nowrap;">RFQ NO :</td>
                            <td style="width: 47%;"><div class="meta-box">{{ $rfqNo }}</div></td>
                            <td style="width: 12%; text-align: right; white-space: nowrap;">SALES :</td>
                            <td style="width: 26%; padding-right: 8px;"><div class="meta-box">{{ $salesName }}</div></td>
                        </tr>
                        <tr>
                            <td colspan="2" style="padding-left: 8px; white-space: nowrap;">
                                DATELINE REQUEST :
                                <span class="meta-box-sm">{{ $datelineDays }}</span>
                                DAYS
                            </td>
                            <td style="text-align: right; white-space: nowrap;">ORDER CODE :</td>
                            <td style="padding-right: 8px;"><div class="meta-box">{{ $orderCode }}</div></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        {{-- BAGIAN UTAMA: DATA SURVEY & ORDER REQUEST SPESIFICATION --}}
        <table class="body-grid">
            <tr>
                {{-- KOLOM KIRI: DATA SURVEY --}}
                <td class="col-left">
                    <div class="panel-header">DATA SURVEY</div>
                    <div class="data-survey-content">
                        <div class="item-block">
                            <div class="item-title">Jenis &amp; Tujuan Survei</div>
                            <div class="item-text">
                                <strong>{{ $survey->survey_type ?: 'Survei Lapangan' }}</strong>
                                @if ($survey->purpose)
                                    — {{ $survey->purpose }}
                                @endif
                            </div>
                        </div>

                        <div class="item-block">
                            <div class="item-title">Hasil Pemeriksaan Lapangan</div>
                            <div class="item-text">{!! nl2br(e($survey->findings ?: '-')) !!}</div>
                        </div>

                        <div class="item-block">
                            <div class="item-title">Kondisi Lokasi</div>
                            <div class="item-text">{!! nl2br(e($survey->location_condition ?: '-')) !!}</div>
                        </div>

                        @if ($survey->result_notes)
                            <div class="item-block">
                                <div class="item-title">Catatan Hasil Survei</div>
                                <div class="item-text">{!! nl2br(e($survey->result_notes)) !!}</div>
                            </div>
                        @endif

                        <div class="item-block">
                            <div class="item-title">Kesimpulan &amp; Rekomendasi</div>
                            <div class="item-text">{!! nl2br(e($survey->conclusion ?: '-')) !!}</div>
                        </div>
                    </div>
                </td>

                {{-- KOLOM KANAN: ORDER REQUEST SPESIFICATION + TANDA TANGAN --}}
                <td class="col-right">
                    <div class="panel-header">ORDER REQUEST SPESIFICATION</div>
                    <div class="order-spec-content">
                        <div class="item-block">
                            <div class="item-title">Nama Kegiatan / Event</div>
                            <div class="item-text"><strong>{{ $survey->order?->event_name ?: '-' }}</strong></div>
                        </div>

                        @if ($survey->order?->event_date)
                            <div class="item-block">
                                <div class="item-title">Tanggal Pelaksanaan Event</div>
                                <div class="item-text">{{ $survey->order->event_date->translatedFormat('d F Y') }}</div>
                            </div>
                        @endif

                        <div class="item-block">
                            <div class="item-title">Spesifikasi &amp; Kebutuhan Pesanan</div>
                            <div class="item-text">{!! nl2br(e($survey->order?->description ?: '-')) !!}</div>
                        </div>

                        @if ($survey->notes)
                            <div class="item-block">
                                <div class="item-title">Keterangan Tambahan Pengajuan</div>
                                <div class="item-text">{!! nl2br(e($survey->notes)) !!}</div>
                            </div>
                        @endif
                    </div>

                    {{-- TANDA TANGAN SURVEYOR & CUSTOMER --}}
                    <table class="sig-grid">
                        <tr>
                            <th>SURVEYOR</th>
                            <th>CUSTOMER</th>
                        </tr>
                        <tr>
                            <td>
                                <div class="sig-img-wrap"></div>
                                <div class="sig-name">( {{ $survey->assignedUser?->name ?? '................................' }} )</div>
                                <div class="sig-time">{{ $survey->executed_on?->format('d/m/Y') ?? '-' }}</div>
                            </td>
                            <td>
                                <div class="sig-img-wrap">
                                    @if ($signature)
                                        <img src="{{ $signature }}" class="sig-img" alt="Tanda Tangan Customer">
                                    @elseif ($survey->isSigned())
                                        <div style="border: 1px dashed #10b981; background: #ecfdf5; color: #065f46; padding: 6px; margin: 8px auto 0 auto; width: 85%; font-size: 7.5pt;">
                                            <strong>TERTANDATANGANI DIGITAL</strong><br>
                                            <span>Sah &amp; tersimpan di sistem</span>
                                        </div>
                                    @endif
                                </div>
                                <div class="sig-name">( {{ $survey->signer_name ?: $customerName }} )</div>
                                @if ($survey->signed_at)
                                    <div class="sig-time">{{ $survey->signed_at->format('d/m/Y H:i') }} WIB</div>
                                @endif
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>

    {{-- HALAMAN 2 (OPSIONAL): DOKUMENTASI FOTO LAPANGAN --}}
    @if ($photos && $photos->isNotEmpty())
        <div class="page-break"></div>
        <div class="outer-frame" style="padding: 12px;">
            <div style="font-size: 11pt; font-weight: bold; text-transform: uppercase; border-bottom: 1.5px solid #111111; padding-bottom: 5px;">
                LAMPIRAN DOKUMENTASI FOTO LAPANGAN — {{ $rfqNo }}
            </div>
            <table class="photos-grid">
                @foreach ($photos->chunk(3) as $row)
                    <tr>
                        @foreach ($row as $p)
                            <td>
                                <img src="{{ $p['src'] }}" alt="{{ $p['caption'] }}">
                                <div class="photo-cap">{{ $p['caption'] }}</div>
                            </td>
                        @endforeach
                        @for ($i = count($row); $i < 3; $i++)
                            <td></td>
                        @endfor
                    </tr>
                @endforeach
            </table>
        </div>
    @endif
</body>
</html>
