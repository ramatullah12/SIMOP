<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Tugas Survey - {{ $survey->sps_number }}</title>
    <style>
        @page {
            margin: 28px 42px 36px 42px;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 10.5pt;
            color: #111111;
            line-height: 1.5;
            margin: 0;
        }
        /* Kop Surat CV. Media Solution */
        .kop-table {
            width: 100%;
            border-collapse: collapse;
        }
        .kop-table td {
            vertical-align: top;
            padding: 0;
        }
        .kop-logo {
            width: 30%;
            padding-right: 10px;
        }
        .kop-logo img {
            width: 155px;
            height: auto;
        }
        .kop-info {
            width: 70%;
            text-align: right;
        }
        .kop-company {
            font-family: 'Times New Roman', Times, serif;
            font-size: 19pt;
            font-weight: bold;
            letter-spacing: 0.5px;
            color: #000000;
            line-height: 1.05;
            margin-bottom: 2px;
        }
        .kop-services {
            font-family: 'Times New Roman', Times, serif;
            font-size: 7.4pt;
            font-weight: bold;
            color: #111111;
            line-height: 1.25;
        }
        .kop-address {
            font-size: 7.2pt;
            color: #222222;
            line-height: 1.3;
            margin-top: 2px;
        }
        .kop-line-red {
            border-top: 2px solid #dc2626;
            margin-top: 6px;
        }
        .kop-line-green {
            border-top: 1.5px solid #15803d;
            margin-top: 2px;
            margin-bottom: 26px;
        }

        /* Judul Surat */
        .surat-title {
            text-align: center;
            font-size: 13pt;
            font-weight: bold;
            text-transform: uppercase;
            text-decoration: underline;
            letter-spacing: 0.4px;
            margin-bottom: 4px;
        }
        .surat-nomor {
            text-align: center;
            font-size: 10pt;
            margin-bottom: 32px;
        }

        /* Bagian Isi */
        .section-title {
            font-weight: bold;
            margin: 22px 0 8px 0;
        }
        .section-title.first {
            margin-top: 0;
        }
        table.fields {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 6px;
        }
        table.fields td {
            padding: 3.5px 2px;
            vertical-align: top;
            font-size: 10pt;
        }
        table.fields td.label {
            width: 19%;
            font-weight: bold;
        }
        table.fields td.colon {
            width: 3%;
            text-align: center;
            font-weight: bold;
        }
        table.fields td.value {
            width: 78%;
        }

        .penutup {
            font-weight: bold;
            margin-top: 28px;
        }

        /* Tanda Tangan */
        .ttd-wrap {
            width: 100%;
            border-collapse: collapse;
            margin-top: 38px;
        }
        .ttd-wrap td.spacer {
            width: 55%;
        }
        .ttd-wrap td.sign {
            width: 45%;
            text-align: center;
            font-size: 10pt;
        }
        .ttd-date {
            font-weight: bold;
        }
        .ttd-space {
            height: 70px;
        }
        .ttd-name {
            font-weight: bold;
            text-decoration: underline;
        }
    </style>
</head>
<body>
    @php
        $companyAddress = 'Jl. AKBP Cek Agus No. 12 RT 06 RW 01 Kec. IT II Palembang';
        $approverName = $survey->approver?->name ?? 'Harry Satya Nugraha';
        $approverPosition = $approverEmployee?->position ?: 'Direktur';
        $approverAddress = $approverEmployee?->address ?: $companyAddress;

        $officerName = $survey->assignedUser?->name ?? '-';
        $officerPosition = $officerEmployee?->position ?: 'Petugas Survei Lapangan';
        $officerAddress = $officerEmployee?->address ?: $companyAddress;

        $rfqNo = $survey->order?->order_number ?? '-';
        $tanggalSurvei = ($survey->scheduled_at ?? $survey->sps_issued_at ?? $survey->created_at)?->translatedFormat('d F Y') ?? '-';
        $perihal = trim(implode(' - ', array_filter([
            $survey->survey_type ?: 'Survei Lapangan',
            $survey->order?->event_name,
            $survey->purpose,
        ])));
        $lokasi = $survey->location ?: ($survey->order?->event_location ?? '-');
        $tanggalTerbit = ($survey->sps_issued_at ?? $survey->approved_at ?? now())->translatedFormat('d F Y');
    @endphp

    {{-- KOP SURAT --}}
    <table class="kop-table">
        <tr>
            <td class="kop-logo">
                @if (! empty($logoDataUri))
                    <img src="{{ $logoDataUri }}" alt="Media Solution">
                @else
                    <div style="line-height: 1.1;">
                        <span style="font-size: 16pt; font-weight: bold; color: #3898d4;">Media</span><span style="font-size: 16pt; font-weight: bold; color: #e88924;">Solution</span>
                        <div style="font-size: 7pt; color: #4b5563; margin-top: 1px;">All Media Design &amp; Working</div>
                        <div style="font-family: 'Times New Roman', Times, serif; font-style: italic; font-size: 7.5pt; color: #374151; margin-top: 3px;">Client Satisfaction is Our Commitment</div>
                    </div>
                @endif
            </td>
            <td class="kop-info">
                <div class="kop-company">CV.MEDIA SOLUTION</div>
                <div class="kop-services">
                    ADVERTISING, EVENT ORGANIZER, BIRTHDAY ORGANIZER, EVENT PRODUCTION SERVICE,<br>
                    ARTIST MANAGEMENT, EVENT EQUIPMENT RENT, MULTIMEDIA, &amp; TALENT AGENCY
                </div>
                <div class="kop-address">
                    Jl. AKBP CEK AGUS No 12 Rt 06 Rw 1 Kec IT II Palembang<br>
                    Tlp. : 0711-712636 / 081268026000
                </div>
            </td>
        </tr>
    </table>
    <div class="kop-line-red"></div>
    <div class="kop-line-green"></div>

    {{-- JUDUL --}}
    <div class="surat-title">SURAT TUGAS SURVEY</div>
    <div class="surat-nomor">NO : {{ $survey->sps_number }}</div>

    {{-- PEMBERI TUGAS --}}
    <div class="section-title first">Yang bertandatangan dibawah ini :</div>
    <table class="fields">
        <tr>
            <td class="label">Nama</td>
            <td class="colon">:</td>
            <td class="value">{{ $approverName }}</td>
        </tr>
        <tr>
            <td class="label">Jabatan</td>
            <td class="colon">:</td>
            <td class="value">{{ $approverPosition }}</td>
        </tr>
        <tr>
            <td class="label">Alamat</td>
            <td class="colon">:</td>
            <td class="value">{{ $approverAddress }}</td>
        </tr>
    </table>

    {{-- PENERIMA TUGAS --}}
    <div class="section-title">Dengan ini memberikan Tugas kepada :</div>
    <table class="fields">
        <tr>
            <td class="label">Nama</td>
            <td class="colon">:</td>
            <td class="value">{{ $officerName }}</td>
        </tr>
        <tr>
            <td class="label">Jabatan</td>
            <td class="colon">:</td>
            <td class="value">{{ $officerPosition }}</td>
        </tr>
        <tr>
            <td class="label">Alamat</td>
            <td class="colon">:</td>
            <td class="value">{{ $officerAddress }}</td>
        </tr>
    </table>

    {{-- RINCIAN TUGAS --}}
    <div class="section-title">Untuk Melaksanakan Pekerjaan Survey, merujuk :</div>
    <table class="fields">
        <tr>
            <td class="label">RFQ NO</td>
            <td class="colon">:</td>
            <td class="value">{{ $rfqNo }}</td>
        </tr>
        <tr>
            <td class="label">Tanggal</td>
            <td class="colon">:</td>
            <td class="value">{{ $tanggalSurvei }}</td>
        </tr>
        <tr>
            <td class="label">Perihal</td>
            <td class="colon">:</td>
            <td class="value">{{ $perihal ?: '-' }}</td>
        </tr>
        <tr>
            <td class="label">Lokasi</td>
            <td class="colon">:</td>
            <td class="value">{{ $lokasi }}</td>
        </tr>
    </table>

    {{-- PENUTUP --}}
    <div class="penutup">
        Demikian Surat Tugas ini dibuat, untuk digunakan sebagaimana mestinya.
    </div>

    {{-- TANDA TANGAN --}}
    <table class="ttd-wrap">
        <tr>
            <td class="spacer"></td>
            <td class="sign">
                <div class="ttd-date">Palembang, {{ $tanggalTerbit }}</div>
                <div class="ttd-space"></div>
                <div class="ttd-name">{{ $approverName }}</div>
            </td>
        </tr>
    </table>
</body>
</html>
