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
            font-family: Arial, sans-serif;
            font-size: 10px;
            color: #000;
            line-height: 1.3;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .font-bold {
            font-weight: bold;
        }

        .uppercase {
            text-transform: uppercase;
        }

        .event-container {
            page-break-after: always;
        }

        .event-container:last-child {
            page-break-after: avoid;
        }

        .header-section {
            width: 100%;
            margin-bottom: 5px;
        }

        .header-section td {
            vertical-align: middle;
        }

        .event-info h2,
        .event-info h3 {
            margin: 2px 0;
            text-transform: uppercase;
        }

        .main-title {
            font-size: 18px;
            font-weight: bold;
            margin: 5px 0;
            display: block;
        }

        .acara-table {
            width: 100%;
            border-bottom: 2px solid #000;
            margin-top: 15px;
            margin-bottom: 5px;
            padding-bottom: 2px;
        }

        .acara-table td {
            font-size: 13px;
            font-weight: bold;
        }

        .seri-label {
            font-weight: bold;
            font-size: 11px;
            margin: 8px 0 4px 0;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #000;
        }

        .data-table th {
            background-color: #f2f2f2;
            border: 1px solid #000;
            padding: 4px;
            font-size: 9px;
            text-align: left;
        }

        .data-table td {
            border: 1px solid #000;
            padding: 4px;
            vertical-align: top;
        }

        .footer {
            position: fixed;
            bottom: 0px;
            width: 100%;
            font-size: 10px;
            text-align: right;
            border-top: 1px solid #ccc;
            padding-top: 5px;
        }
    </style>
</head>

<body style="width: 100%; margin: auto;">

    @foreach ($globalData as $item)
        @php
            $event = $item['event'];
            $dataAcara = $item['dataAcara'];
        @endphp

        <div class="event-container">
            @php
                $pathKiri = $event['logo_kiri'] ? ROOT_DIR . '/private-uploads/logos/' . $event['logo_kiri'] : $_SERVER['DOCUMENT_ROOT'] . '/public/assets/ico/icon-bar.png';
                $pathKanan = $event['logo_kanan'] ? ROOT_DIR . '/private-uploads/logos/' . $event['logo_kanan'] : $_SERVER['DOCUMENT_ROOT'] . '/public/assets/ico/logo.png';

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

            <table class="header-section">
                <tr>
                    <td width="100">
                        @if ($logoSrcKiri)
                            <img src="{{ $logoSrcKiri }}" style="max-height: 80px; max-width: 100px;">
                        @endif
                    </td>
                    <td class="text-center event-info">
                        <h2 style="font-size: 20px; color: #1e293b; margin-bottom: 5px;">{{ $event['nama_event'] }}</h2>
                        <h3 style="font-size: 14px; color: #64748b;">{{ $event['lokasi_event'] }}</h3>
                        <p style="margin: 5px 0; font-weight: bold; font-size: 12px; color: #334155;">
                            {{ \Carbon\Carbon::parse($event['tanggal_mulai'])->translatedFormat('d F Y') }}
                        </p>
                        <div class="main-title" style="margin-top: 10px; border-top: 2px solid #000; display: inline-block; padding: 5px 20px;">BUKU ACARA</div>
                    </td>
                    <td width="100" class="text-right">
                        @if ($logoSrcKanan)
                            <img src="{{ $logoSrcKanan }}" style="max-height: 80px; max-width: 100px;">
                        @endif
                    </td>
                </tr>
            </table>

            <div style="height: 10px; border-bottom: 3px double #000; margin-bottom: 15px;"></div>

            @foreach ($dataAcara as $acara)
                <div style="page-break-inside: avoid; margin-bottom: 25px;">
                    <table class="acara-table" style="background-color: #f8fafc; border: 1px solid #e2e8f0; margin-bottom: 10px;">
                        <tr>
                            <td width="20%" style="font-size: 14px;">Acara {{ $acara['nomor_acara'] }}</td>
                            <td width="60%" class="text-center uppercase" style="font-size: 16px; letter-spacing: 1px;">{{ $acara['nama_acara'] }}</td>
                            <td width="20%" class="text-right" style="font-size: 14px;">{{ $acara['kode_ku'] }}</td>
                        </tr>
                    </table>

                    @foreach ($acara['seri'] as $seriNum => $athletes)
                        <div style="margin-bottom: 15px;">
                            <div class="seri-label" style="background-color: #334155; color: white; display: inline-block; padding: 3px 15px; border-radius: 4px 4px 0 0;">Seri {{ $seriNum }}</div>
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th width="8%" class="text-center">Lin</th>
                                        <th width="35%">Nama Atlet</th>
                                        <th width="10%" class="text-center">Tahun</th>
                                        <th width="27%">Klub / Perkumpulan</th>
                                        <th width="12%" class="text-center">Prestasi</th>
                                        <th width="8%" class="text-center">Hasil</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $laneMap = [];
                                        foreach ($athletes as $at) {
                                            $laneMap[$at['nomor_lintasan']] = $at;
                                        }
                                        $startLane = 1;
                                        $endLane = $event['jumlah_lintasan'] > 0 ? $event['jumlah_lintasan'] : 10;
                                    @endphp

                                    @for ($i = $startLane; $i <= $endLane; $i++)
                                        @php $athlete = $laneMap[$i] ?? null; @endphp
                                        <tr style="{{ $athlete ? '' : 'background-color: #fdfdfd; color: #ccc;' }}">
                                            <td class="text-center font-bold" style="font-size: 11px;">{{ $i }}</td>
                                            <td class="font-bold uppercase" style="font-size: 11px;">{{ $athlete ? $athlete['nama_lengkap'] : '-' }}</td>
                                            <td class="text-center">{{ $athlete ? date('Y', strtotime($athlete['tanggal_lahir'])) : '-' }}</td>
                                            <td class="uppercase" style="font-size: 10px;">{{ $athlete ? $athlete['klub_renang'] : '-' }}</td>
                                            <td class="text-center font-bold" style="font-size: 11px; font-family: 'Courier New', Courier, monospace;">
                                                {{ $athlete ? $athlete['prestasi'] : '-' }}
                                            </td>
                                            <td class="text-center font-bold" style="border-left: 1px solid #000; font-size: 11px; font-family: 'Courier New', Courier, monospace;">
                                                {{ $athlete ? $athlete['hasil'] : '' }}
                                            </td>
                                        </tr>
                                    @endfor
                                </tbody>
                            </table>
                        </div>
                    @endforeach
                </div>
            @endforeach
        </div>
    @endforeach

    <div class="footer">
        Page <span class="page-number"></span> | Dicetak pada {{ date('d/m/Y H:i') }} | Khafid Swimming Club (KSC)
    </div>
    

</body>

</html>
