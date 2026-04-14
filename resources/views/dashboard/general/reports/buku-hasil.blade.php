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

        .data-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #000;
        }

        .data-table th {
            background-color: #f2f2f2;
            border: 1px solid #000;
            padding: 6px 4px;
            font-size: 10px;
            text-align: left;
            text-transform: uppercase;
        }

        .data-table td {
            border: 1px solid #000;
            padding: 6px 4px;
            vertical-align: middle;
        }

        .rank-highlight {
            font-weight: bold;
        }

        .rank-1 { background-color: #fffbeb; } /* Subtle Gold */
        .rank-2 { background-color: #f8fafc; } /* Subtle Silver */
        .rank-3 { background-color: #fff7ed; } /* Subtle Bronze */

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
                    <h2 style="font-size: 18px; color: #1e293b; margin-bottom: 5px;">{{ $event['nama_event'] }}</h2>
                    <h3 style="font-size: 12px; color: #64748b;">{{ $event['lokasi_event'] }}</h3>
                    <p style="margin: 5px 0; font-weight: bold; font-size: 11px; color: #334155;">
                        {{ \Carbon\Carbon::parse($event['tanggal_mulai'])->translatedFormat('d F Y') }}
                    </p>
                    <div class="main-title" style="margin-top: 5px; border-top: 2px solid #000; display: inline-block; padding: 5px 20px;">BUKU HASIL</div>
                </td>
                <td width="100" class="text-right">
                    @if ($logoSrcKanan)
                        <img src="{{ $logoSrcKanan }}" style="max-height: 80px; max-width: 100px;">
                    @endif
                </td>
            </tr>
        </table>

        <div style="height: 5px; border-bottom: 2px double #000; margin-bottom: 15px;"></div>

        @foreach ($globalData as $item)
            <div style="page-break-inside: avoid; margin-bottom: 30px;">
                <table class="acara-table" style="background-color: #f8fafc; border: 1px solid #e2e8f0; margin-bottom: 5px;">
                    <tr>
                        <td width="20%" style="font-size: 12px;">Acara {{ $item['acara']['nomor_acara'] }}</td>
                        <td width="60%" class="text-center uppercase" style="font-size: 14px; letter-spacing: 1px;">{{ $item['acara']['nama_acara'] }}</td>
                        <td width="20%" class="text-right" style="font-size: 12px;">{{ $item['acara']['category']['kode_ku'] ?? 'UMUM' }}</td>
                    </tr>
                </table>

                <table class="data-table">
                    <thead>
                        <tr>
                            <th width="8%" class="text-center">POS</th>
                            <th width="35%">NAMA ATLET / SISWA</th>
                            <th width="37%">ASAL KLUB / SEKOLAH / MADRASAH</th>
                            <th width="20%" class="text-center">HASIL</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($item['results'] as $index => $res)
                            @php
                                $isTop3 = $res['status'] === 'FINISH' && ($index < 3);
                                $rowClass = '';
                                if ($isTop3) {
                                    $rowClass = 'rank-' . ($index + 1);
                                }
                            @endphp
                            <tr class="{{ $rowClass }}">
                                <td class="text-center font-bold" style="font-size: 12px;">
                                    @if ($res['status'] === 'FINISH')
                                        {{ $index + 1 }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="uppercase {{ $isTop3 ? 'font-bold' : '' }}" style="font-size: 11px;">
                                    {{ $res['nama_lengkap'] }}
                                </td>
                                <td class="uppercase" style="font-size: 10px;">
                                    {{ $res['sekolah'] ?: ($res['klub_renang'] ?: '-') }}
                                </td>
                                <td class="text-center font-bold" style="font-size: 12px; font-family: 'Courier New', Courier, monospace;">
                                    {{ $res['status'] === 'FINISH' ? $res['waktu_akhir'] : $res['status'] }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center" style="padding: 10px; color: #666;">Data hasil belum tersedia untuk acara ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @endforeach
    </div>

    <div class="footer">
        Page <span class="page-number"></span> | Dicetak pada {{ date('d/m/Y H:i') }} | Khafid Swimming Club (KSC)
    </div>

</body>

</html>
