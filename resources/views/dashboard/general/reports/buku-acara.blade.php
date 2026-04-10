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
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .uppercase { text-transform: uppercase; }

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
        .event-info h2, .event-info h3 {
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

    @foreach($globalData as $item)
        @php 
            $event = $item['event'];
            $dataAcara = $item['dataAcara'];
        @endphp

        <div class="event-container">
            @php
                $logoPath = $_SERVER['DOCUMENT_ROOT'] . '/public/assets/ico/icon-bar.png';
                if (file_exists($logoPath)) {
                    $logoData = base64_encode(file_get_contents($logoPath));
                    $logoSrc = 'data:image/png;base64,' . $logoData;
                } else {
                    $logoSrc = '';
                }
            @endphp

            {{-- HEADER WITH LOGOS --}}
            <table class="header-section">
                <tr>
                    <td width="80">
                        @if($logoSrc)
                            <img src="{{ $logoSrc }}" width="70">
                        @endif
                    </td>
                    <td class="text-center event-info">
                        <h2 style="font-size: 16px;">{{ $event['nama_event'] }}</h2>
                        <h3 style="font-size: 12px;">{{ $event['lokasi_event'] }}</h3>
                        <p style="margin: 0; font-weight: bold;">{{ date('d F Y', strtotime($event['tanggal_mulai'])) }}</p>
                        <div class="main-title">BUKU ACARA</div>
                    </td>
                    <td width="80" class="text-right">
                        @if($logoSrc)
                            <img src="{{ $logoSrc }}" width="70">
                        @endif
                    </td>
                </tr>
            </table>

            <hr style="border: 1px solid #000;">

            @foreach($dataAcara as $acara)
                <table class="acara-table">
                    <tr>
                        <td width="30%">Acara {{ $acara['nomor_acara'] }}</td>
                        <td class="text-center uppercase" width="40%">{{ $acara['nama_acara'] }}</td>
                        <td class="text-right" width="30%">{{ $acara['kode_ku'] }}</td>
                    </tr>
                </table>

                @foreach($acara['seri'] as $seriNum => $athletes)
                    <div class="seri-label">Seri {{ $seriNum }}</div>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th width="30" class="text-center">Lint</th>
                                <th>Nama Lengkap Atlet</th>
                                <th width="40" class="text-center">Lahir</th>
                                <th width="180">Tim/Club</th>
                                <th width="60" class="text-center">Prestasi</th>
                                <th width="60" class="text-center">Hasil</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $laneMap = [];
                                foreach($athletes as $at) { 
                                    // Gunakan nomor lintasan langsung dari DB karena sudah mulai dari 1
                                    $laneMap[$at['nomor_lintasan']] = $at; 
                                }
                                $startLane = 1;
                                $endLane = ($event['jumlah_lintasan'] > 0) ? $event['jumlah_lintasan'] : 10;
                            @endphp

                            @for($i = $startLane; $i <= $endLane; $i++)
                                @php $athlete = $laneMap[$i] ?? null; @endphp
                                <tr>
                                    <td class="text-center">{{ $i }}</td>
                                    <td class="font-bold uppercase">{{ $athlete ? $athlete['nama_lengkap'] : '-' }}</td>
                                    <td class="text-center">{{ $athlete ? date('Y', strtotime($athlete['tanggal_lahir'])) : '-' }}</td>
                                    <td class="uppercase">{{ $athlete ? $athlete['klub_renang'] : '-' }}</td>
                                    <td class="text-center">{{ ($athlete && $athlete['seed_time']) ? $athlete['seed_time'] : '' }}</td>
                                    <td></td>
                                </tr>
                            @endfor
                        </tbody>
                    </table>
                @endforeach
            @endforeach
        </div>
    @endforeach

    <div class="footer">
        Page <span class="page-number"></span> | Centrum SC
    </div>

</body>
</html>
