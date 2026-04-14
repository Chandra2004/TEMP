<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $title }}</title>
    <style>
        @page {
            margin: 1cm;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 10px;
            color: #333;
            line-height: 1.4;
        }
        .header {
            width: 100%;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        .header table { width: 100%; }
        .header .logo { width: 80px; }
        .header .event-info { text-align: center; }
        .header h1 { margin: 0; font-size: 16px; text-transform: uppercase; }
        .header p { margin: 2px 0; font-size: 11px; font-weight: bold; }
        
        .report-title {
            text-align: center;
            font-size: 14px;
            font-weight: bold;
            margin: 15px 0;
            text-transform: uppercase;
            text-decoration: underline;
        }

        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table.data-table th {
            background-color: #f8fafc;
            border-top: 1px solid #000;
            border-bottom: 1px solid #000;
            padding: 8px 5px;
            font-size: 9px;
            text-align: left;
            text-transform: uppercase;
        }
        table.data-table td {
            padding: 6px 5px;
            border-bottom: 0.5px solid #e5e7eb;
            vertical-align: top;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }

        .summary-box {
            float: right;
            width: 250px;
            margin-top: 10px;
            border: 1px solid #000;
            padding: 10px;
        }

        .signature {
            margin-top: 50px;
            float: right;
            width: 200px;
            text-align: center;
        }
        
        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            font-size: 8px;
            border-top: 0.5px solid #ccc;
            padding-top: 5px;
        }
    </style>
</head>
<body>
    @php
        $pathKiri = (isset($event) && $event['logo_kiri']) ? ROOT_DIR . '/private-uploads/logos/' . $event['logo_kiri'] : $_SERVER['DOCUMENT_ROOT'] . '/public/assets/ico/icon-bar.png';
        $pathKanan = (isset($event) && $event['logo_kanan']) ? ROOT_DIR . '/private-uploads/logos/' . $event['logo_kanan'] : $_SERVER['DOCUMENT_ROOT'] . '/public/assets/ico/logo.png';

        $logoSrcKiri = '';
        if (file_exists($pathKiri)) {
            $logoDataKiri = base64_encode(file_get_contents($pathKiri));
            $mimeKiri = mime_content_type($pathKiri);
            $logoSrcKiri = 'data:' . $mimeKiri . ';base64,' . $logoDataKiri;
        }

        $logoSrcKanan = '';
        if (file_exists($pathKanan)) {
            $logoDataKanan = base64_encode(file_get_contents($pathKanan));
            $mimeKanan = mime_content_type($pathKanan);
            $logoSrcKanan = 'data:' . $mimeKanan . ';base64,' . $logoDataKanan;
        }
    @endphp

    <div class="header">
        <table style="border-collapse: collapse;">
            <tr>
                <td class="logo" width="100">
                    @if($logoSrcKiri)
                        <img src="{{ $logoSrcKiri }}" style="max-height: 80px; max-width: 100px;">
                    @endif
                </td>
                <td class="event-info text-center">
                    @if(isset($event))
                        <h1 style="font-size: 20px; color: #1e293b; margin: 0;">{{ $event['nama_event'] }}</h1>
                        <p style="margin: 3px 0; font-size: 13px; color: #64748b;">{{ $event['lokasi_event'] }}</p>
                        <p style="margin: 3px 0; font-weight: bold; color: #334155;">{{ \Carbon\Carbon::parse($event['tanggal_mulai'])->translatedFormat('d F Y') }}</p>
                    @else
                        <h1 style="font-size: 20px; color: #1e293b; margin: 0;">KHAFID SWIMMING CLUB (KSC)</h1>
                        <p style="margin: 3px 0; font-size: 13px; color: #64748b;">LAPORAN PENDAFTARAN GLOBAL</p>
                    @endif
                </td>
                <td class="logo text-right" width="100">
                    @if($logoSrcKanan)
                        <img src="{{ $logoSrcKanan }}" style="max-height: 80px; max-width: 100px;">
                    @endif
                </td>
            </tr>
        </table>
        <div style="height: 5px; border-bottom: 3px double #000; margin-top: 10px;"></div>
    </div>

    <div class="report-title">REKAPITULASI PENDAFTARAN ATLET</div>

    <table class="data-table">
        <thead>
            <tr>
                <th class="text-center" width="20">No</th>
                <th width="70">Reg ID</th>
                <th>Nama Lengkap Atlet</th>
                <th class="text-center" width="30">Lahir</th>
                <th class="text-center" width="20">JK</th>
                <th width="120">Acara / Kategori</th>
                <th class="text-right" width="80">Biaya</th>
                <th class="text-center" width="60">Status</th>
            </tr>
        </thead>
        <tbody>
            @php $totalBiaya = 0; @endphp
            @forelse($registrations as $index => $reg)
                @php $totalBiaya += (float) ($reg['biaya_pendaftaran'] ?? 0); @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="font-bold">{{ $reg['nomor_pendaftaran'] }}</td>
                    <td class="font-bold uppercase">{{ $reg['nama_lengkap'] }}</td>
                    <td class="text-center">{{ date('Y', strtotime($reg['tanggal_lahir'])) }}</td>
                    <td class="text-center">{{ $reg['jenis_kelamin'] }}</td>
                    <td>
                        <div class="font-bold">{{ $reg['nama_acara'] }}</div>
                        <div style="font-size: 8px; color: #666;">{{ $reg['nama_event'] }}</div>
                    </td>
                    <td class="text-right">Rp {{ number_format((float)($reg['biaya_pendaftaran'] ?? 0), 0, ',', '.') }}</td>
                    <td class="text-center">
                        <span class="font-bold uppercase">{{ $reg['status_pendaftaran'] ?? 'PENDING' }}</span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center">Tidak ada data pendaftaran ditemukan.</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr style="background-color: #f8fafc; font-weight: bold;">
                <td colspan="6" class="text-right" style="padding: 10px; border-top: 1.5px solid #000;">TOTAL PENERIMAAN :</td>
                <td class="text-right" style="padding: 10px; border-top: 1.5px solid #000;">
                    Rp {{ number_format($totalBiaya, 0, ',', '.') }}
                </td>
                <td style="border-top: 1.5px solid #000;"></td>
            </tr>
        </tfoot>
    </table>

    <div style="clear: both;"></div>

    <div class="signature">
        <p>Sidoarjo, {{ date('d F Y') }}</p>
        <p style="margin-bottom: 60px;">Administrator KSC,</p>
        <p><strong>( ________________________ )</strong></p>
    </div>

    <div class="footer">
        <table width="100%">
            <tr>
                <td width="50%">Laporan dihasilkan pada: {{ date('d/m/Y H:i') }} WIB</td>
                <td width="50%" style="text-align: right;">Halaman 1 dari 1</td>
            </tr>
        </table>
    </div>
</body>
</html>
