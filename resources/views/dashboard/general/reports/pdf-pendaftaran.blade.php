{{-- <!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        body {
            font-family: 'Helvetica', sans-serif;
            font-size: 11px;
            color: #333;
            line-height: 1.4;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            position: relative;
        }

        .header h1 {
            font-size: 18px;
            text-transform: uppercase;
            margin: 0;
            font-weight: 900;
        }

        .header h2 {
            font-size: 14px;
            margin: 5px 0;
            font-weight: 700;
        }

        .header p {
            margin: 2px 0;
            font-size: 12px;
        }

        .logo-left {
            position: absolute;
            top: 0;
            left: 0;
            width: 80px;
        }

        .logo-right {
            position: absolute;
            top: 0;
            right: 0;
            width: 80px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th {
            background-color: #f8fafc;
            border-top: 1px solid #333;
            border-bottom: 1px solid #333;
            padding: 8px 4px;
            text-align: left;
            text-transform: uppercase;
            font-weight: 900;
            font-size: 10px;
        }

        td {
            padding: 8px 4px;
            border-bottom: 1px solid #eee;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .footer {
            margin-top: 30px;
        }

        .footer-table {
            width: 100%;
            border: none;
        }

        .footer-table td {
            border: none;
            padding: 2px 0;
            vertical-align: top;
        }

        .signature {
            margin-top: 50px;
            text-align: right;
        }

        .total-box {
            font-weight: 900;
            font-size: 14px;
            margin-top: 10px;
        }
    </style>
</head>

<body>
    <div class="header">
        @if ($event)
            <h1>{{ $event['nama_event'] }}</h1>
            <h2>{{ $event['lokasi_event'] }}</h2>
            <p>{{ \Carbon\Carbon::parse($event['tanggal_event'])->translatedFormat('d F Y') }}</p>
        @else
            <h1>KSC SWIMMING CLUB</h1>
            <h2>REKAPITULASI PENDAFTARAN SELURUH EVENT</h2>
        @endif
        <h1 style="margin-top: 10px; border-top: 2px solid #000; padding-top: 5px;">LIST PENDAFTARAN</h1>
    </div>

    <table>
        <thead>
            <tr>
                <th width="30" class="text-center">No</th>
                <th width="80">Reg ID</th>
                <th>Nama Lengkap</th>
                <th width="40" class="text-center">Lahir</th>
                <th width="30" class="text-center">JK</th>
                <th>Tim/ Club</th>
                <th>Event</th>
                <th class="text-right">Biaya</th>
                <th class="text-center">Status</th>
            </tr>
        </thead>
        <tbody>
            @php $total = 0; @endphp
            @foreach ($registrations as $index => $reg)
                @php $total += $reg['biaya_event']; @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $reg['nomor_pendaftaran'] }}</td>
                    <td>{{ strtoupper($reg['nama_lengkap']) }}</td>
                    <td class="text-center">{{ \Carbon\Carbon::parse($reg['tanggal_lahir'])->format('Y') }}</td>
                    <td class="text-center">{{ $reg['jenis_kelamin'] }}</td>
                    <td>{{ $reg['nama_klub'] ?? 'KSC' }}</td>
                    <td>{{ $reg['nama_event'] }}</td>
                    <td class="text-right">{{ number_format((float) ($reg['biaya_event'] ?? 0), 0, ',', '.') }}</td>
                    <td class="text-center">{{ strtoupper($reg['status']) }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="7" class="text-right" style="font-weight: 900; border-top: 1px solid #333;">TOTAL
                    TAGIHAN :</td>
                <td class="text-right" style="font-weight: 900; border-top: 1px solid #333;">Rp
                    {{ number_format((float) ($total ?? 0), 0, ',', '.') }}</td>
                <td style="border-top: 1px solid #333;"></td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        <table class="footer-table">
            <tr>
                <td width="100">Tim/ Club</td>
                <td>: KHAFID SWIMMING CLUB (KSC)</td>
            </tr>
            <tr>
                <td>Laporan Pada</td>
                <td>: {{ date('d F Y, H:i') }} WIB</td>
            </tr>
            <tr>
                <td>Keterangan</td>
                <td>: Laporan ini dihasilkan secara otomatis oleh sistem Manajemen KSC.</td>
            </tr>
        </table>
    </div>

    <div class="signature">
        <p>Sidoarjo, {{ date('d F Y') }}</p>
        <p style="margin-bottom: 60px;">Administrator KSC,</p>
        <p><strong>( ________________________ )</strong></p>
    </div>
</body>

</html> --}}


<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>

    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11px;
            color: #1f2937;
        }

        .header {
            text-align: center;
            margin-bottom: 25px;
        }

        .header-table {
            width: 100%;
        }

        .header-table td {
            vertical-align: middle;
        }

        .title {
            font-size: 18px;
            font-weight: bold;
            margin: 0;
        }

        .subtitle {
            font-size: 14px;
            margin: 2px 0;
        }

        .event-header {
            margin-top: 10px;
            margin-bottom: 5px;
            font-size: 13px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead tr {
            border-top: 1px solid #333;
            border-bottom: 1px solid #333;
            font-size: 10px;
        }

        th {
            text-align: left;
            padding: 4px;
        }

        td {
            padding: 4px;
        }

        tbody tr {
            border-bottom: 1px solid #e5e7eb;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .footer {
            margin-top: 30px;
        }

        .signature {
            margin-top: 50px;
            text-align: right;
        }
    </style>

</head>

<body>


    <div class="header">

        <table class="header-table">
            <tr>

                <td width="120">
                    <img src="data:image/x-icon;base64,<?= base64_encode(file_get_contents(url('assets/ico/favicon.ico'))) ?>">

                </td>

                <td class="text-center">

                    <h1 class="title">KSC SWIMMING CLUB</h1>
                    <h2 class="subtitle">REKAPITULASI PENDAFTARAN SELURUH EVENT</h2>
                    <h2 class="subtitle"><?= date('d F Y') ?></h2>

                </td>

                <td width="120" class="text-right">
                    <img src="data:image/x-icon;base64,<?= base64_encode(file_get_contents(url('assets/ico/favicon.ico'))) ?>">
                </td>

            </tr>
        </table>

    </div>


    <div class="event-header">
        <span>ACARA 101</span>
        &nbsp;&nbsp;
        <span>KU 2026</span>
    </div>


    <table>

        <thead>
            <tr>
                <th class="text-center">Lint</th>
                <th class="text-center">No</th>
                <th>Reg ID</th>
                <th>Nama Lengkap</th>
                <th class="text-center">Lahir</th>
                <th class="text-center">JK</th>
                <th>Tim/ Club</th>
                <th>Event</th>
                <th class="text-right">Biaya</th>
                <th class="text-center">Status</th>
                <th class="text-center">Prestasi</th>
                <th class="text-center">Hasil</th>
            </tr>
        </thead>


        <tbody>

            @php $total = 0; @endphp

            @foreach ($registrations as $index => $reg)
                @php $total += $reg['biaya_event']; @endphp

                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $reg['nomor_pendaftaran'] }}</td>
                    <td>{{ strtoupper($reg['nama_lengkap']) }}</td>
                    <td class="text-center">{{ date('Y', strtotime($reg['tanggal_lahir'])) }}</td>
                    <td class="text-center">{{ $reg['jenis_kelamin'] }}</td>
                    <td>{{ $reg['nama_klub'] ?? 'INDEPENDEN' }}</td>
                    <td>{{ $reg['nama_event'] }}</td>
                    <td class="text-right">{{ rupiah($reg['biaya_event'], 0, ',', '.') }}</td>
                    <td class="text-center">{{ strtoupper($reg['status']) }}</td>
                    <td class="text-center">NULL</td>
                    <td class="text-center">NULL</td>
                </tr>
            @endforeach

        </tbody>

    </table>



    <div class="footer">

        <table width="100%">

            <tr>
                <td width="120">Tim / Club</td>
                <td>: {{ $user['nama_klub'] }}</td>
            </tr>

            <tr>
                <td>Laporan Pada</td>
                <td>: <?= date('d F Y H:i') ?> WIB</td>
            </tr>

            <tr>
                <td>Keterangan</td>
                <td>: Laporan ini dihasilkan secara otomatis oleh sistem.</td>
            </tr>

        </table>

    </div>


    <div class="signature">

        <p>Sidoarjo, <?= date('d F Y') ?></p>
        <p style="margin-bottom:60px;">Administrator</p>
        <p><strong>( ________________________ )</strong></p>

    </div>


</body>

</html>
