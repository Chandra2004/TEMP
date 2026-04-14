@extends('layouts.layout-dashboard.app')

@section('dashboard-section')
    @if ($user['nama_role'] === 'admin' || $user['nama_role'] === 'superadmin')
        <div class="p-4 md:p-8 overflow-y-auto">
            {{-- Header Section --}}
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
                <div>
                    <h2 class="text-2xl font-black text-slate-900 leading-tight uppercase tracking-tight">Pusat Laporan &
                        Ekspor
                    </h2>
                    <p class="text-sm text-slate-500 font-medium">Analisis data pertumbuhan klub dan performa event swimming
                        club
                    </p>
                </div>
                <div class="flex items-center gap-3">
                    <button type="button" onclick="window.print()"
                        class="flex items-center gap-2 bg-white border border-slate-200 text-slate-700 px-4 py-2.5 rounded-xl font-bold text-xs uppercase tracking-widest transition hover:bg-slate-50 shadow-sm">
                        <i data-lucide="printer" class="w-4 h-4 text-slate-400"></i>
                        Cetak Halaman
                    </button>
                </div>
            </div>

            {{-- Statistical Summary --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div
                    class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-xl shadow-slate-100/50 flex items-center gap-5">
                    <div
                        class="w-14 h-14 bg-blue-50 rounded-2xl flex items-center justify-center text-ksc-blue shadow-inner shadow-blue-100">
                        <i data-lucide="users" class="w-7 h-7"></i>
                    </div>
                    <div>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Total Member
                            Terdaftar
                        </p>
                        <h4 class="text-2xl font-black text-slate-900 tracking-tighter">{{ $totalAnggota ?? 0 }}</h4>
                    </div>
                </div>
                <div
                    class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-xl shadow-slate-100/50 flex items-center gap-5">
                    <div
                        class="w-14 h-14 bg-emerald-50 rounded-2xl flex items-center justify-center text-emerald-600 shadow-inner shadow-emerald-100">
                        <i data-lucide="medal" class="w-7 h-7"></i>
                    </div>
                    <div>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Event Selesai</p>
                        <h4 class="text-2xl font-black text-slate-900 tracking-tighter">{{ $eventSelesai ?? 0 }}</h4>
                    </div>
                </div>
                <div
                    class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-xl shadow-slate-100/50 flex items-center gap-5">
                    <div
                        class="w-14 h-14 bg-amber-50 rounded-2xl flex items-center justify-center text-amber-600 shadow-inner shadow-amber-100">
                        <i data-lucide="trending-up" class="w-7 h-7"></i>
                    </div>
                    <div>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Aktifitas Member</p>
                        <h4 class="text-2xl font-black text-slate-900 tracking-tighter">Normal</h4>
                    </div>
                </div>
            </div>

            {{-- Filter & Export Section --}}
            <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
                {{-- Left: Filter Configuration --}}
                <div class="xl:col-span-1 space-y-6">
                    <div class="bg-white rounded-[2rem] border border-slate-100 shadow-2xl p-6 md:p-8">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-8 h-8 bg-slate-900 rounded-lg flex items-center justify-center text-white">
                                <i data-lucide="filter" class="w-4 h-4"></i>
                            </div>
                            <h3 class="text-lg font-black text-slate-900 uppercase tracking-tight">Konstruksi Laporan</h3>
                        </div>

                        <form id="reportForm" class="space-y-6">
                            <div>
                                <label
                                    class="block text-[11px] font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Jenis
                                    Laporan</label>
                                <select id="report_type"
                                    class="w-full bg-slate-50 border border-slate-200 text-slate-900 text-sm font-bold rounded-xl focus:ring-4 focus:ring-blue-50 focus:border-ksc-blue p-3.5 outline-none transition appearance-none cursor-pointer">
                                    <option value="pendaftaran">Data Pendaftaran Event</option>
                                    <option value="buku_acara">Buku Acara (Program Book)</option>
                                    <option value="buku_hasil">Buku Hasil Lomba (Official Result)</option>
                                    <option value="keanggotaan" disabled>Data Seluruh Keanggotaan (Coming Soon)</option>
                                    <option value="keuangan" disabled>Rekapitulasi Keuangan (Coming Soon)</option>
                                    <option value="presensi" disabled>Laporan Presensi Latihan (Coming Soon)</option>
                                </select>
                            </div>



                            <div>
                                <label
                                    class="block text-[11px] font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Filter
                                    Berdasarkan Event</label>
                                <select id="event_uid"
                                    class="w-full bg-slate-50 border border-slate-200 text-slate-900 text-sm font-bold rounded-xl focus:ring-4 focus:ring-blue-50 focus:border-ksc-blue p-3.5 outline-none transition appearance-none cursor-pointer">
                                    <option value="all">Tampilkan Semua Event</option>
                                    @foreach ($events as $event)
                                        <option value="{{ $event['uid'] }}"
                                            {{ ($filters['event_uid'] ?? '') == $event['uid'] ? 'selected' : '' }}>
                                            {{ $event['nama_event'] }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="pt-4 space-y-3">
                                <button type="button" onclick="handleReportAction('preview')"
                                    class="w-full bg-slate-900 hover:bg-black text-white font-black text-[10px] uppercase tracking-[0.2em] rounded-xl px-6 py-4 shadow-xl shadow-slate-200 transition-all transform hover:-translate-y-1 flex items-center justify-center gap-3">
                                    <i data-lucide="refresh-ccw" class="w-4 h-4"></i>
                                    Generate Preview
                                </button>
                                <div class="grid grid-cols-2 gap-3">
                                    <button type="button" onclick="handleReportAction('excel')"
                                        class="bg-emerald-600 hover:bg-emerald-700 text-white font-black text-[10px] uppercase tracking-widest rounded-xl px-4 py-3.5 transition-all flex items-center justify-center gap-2">
                                        <i data-lucide="file-spreadsheet" class="w-4 h-4"></i>
                                        EXCEL
                                    </button>
                                    <button type="button" onclick="handleReportAction('pdf')"
                                        class="bg-red-600 hover:bg-red-700 text-white font-black text-[10px] uppercase tracking-widest rounded-xl px-4 py-3.5 transition-all flex items-center justify-center gap-2">
                                        <i data-lucide="file-text" class="w-4 h-4"></i>
                                        PDF
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                    {{-- Reporting Notice --}}
                    <div class="bg-amber-50 border border-amber-100 rounded-[2rem] p-6 text-left">
                        <div class="flex gap-4">
                            <div
                                class="w-10 h-10 bg-amber-100 rounded-xl flex items-center justify-center text-amber-600 shrink-0">
                                <i data-lucide="info" class="w-5 h-5"></i>
                            </div>
                            <div>
                                <h5 class="text-sm font-black text-amber-900 uppercase tracking-tight mb-1">Informasi Ekspor
                                </h5>
                                <p class="text-xs text-amber-700 leading-relaxed font-medium">Laporan yang diekspor akan
                                    mencakup seluruh data yang telah divalidasi oleh sistem. Data registrasi "Menunggu"
                                    tidak
                                    akan disertakan dalam rekapitulasi keuangan.</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Right: Data Preview Table --}}
                <div class="xl:col-span-2 space-y-6">
                    <div
                        class="bg-white rounded-[2rem] border border-slate-100 shadow-2xl overflow-hidden flex flex-col h-full">
                        <div class="p-6 md:p-8 flex items-center justify-between border-b border-slate-50 bg-slate-50/50">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 bg-ksc-blue rounded-lg flex items-center justify-center text-white">
                                    <i data-lucide="layout-list" class="w-4 h-4"></i>
                                </div>
                                <h3 class="text-lg font-black text-slate-900 uppercase tracking-tight">Preview Data Terkini
                                </h3>
                            </div>
                            <span
                                class="bg-blue-100 text-ksc-blue text-[10px] font-black uppercase tracking-widest px-3 py-1.5 rounded-full">Top
                                10 Record</span>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead class="bg-white border-b border-slate-100">
                                    <tr>
                                        <th
                                            class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                            Waktu</th>
                                        <th
                                            class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                            Detail Informasi</th>
                                        <th
                                            class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                            Kategori</th>
                                        <th
                                            class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">
                                            Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-50">
                                    @forelse ($previewData as $row)
                                        <tr class="hover:bg-slate-50/80 transition cursor-default">
                                            <td class="px-8 py-6">
                                                <p class="text-xs font-bold text-slate-900">
                                                    {{ date('d M Y', strtotime($row['created_at'])) }}</p>
                                                <p class="text-[10px] text-slate-400 font-medium">
                                                    {{ date('H:i', strtotime($row['created_at'])) }} WIB</p>
                                            </td>
                                            <td class="px-8 py-6">
                                                <div class="flex flex-col">
                                                    <span
                                                        class="text-sm font-black text-slate-800 tracking-tight">{{ $row['nama_event'] }}</span>
                                                    <span class="text-xs text-slate-500 font-bold">Member:
                                                        {{ $row['nama_lengkap'] }}</span>
                                                </div>
                                            </td>
                                            <td class="px-8 py-6">
                                                <span
                                                    class="bg-slate-100 text-slate-600 px-3 py-1.5 rounded-lg text-[10px] font-bold uppercase tracking-tight">Reg
                                                    No: {{ $row['nomor_pendaftaran'] }}</span>
                                            </td>
                                            <td class="px-8 py-6 text-right">
                                                @php
                                                    $statusColor = 'amber';
                                                    if ($row['status_pendaftaran'] === 'diterima') {
                                                        $statusColor = 'emerald';
                                                    }
                                                    if ($row['status_pendaftaran'] === 'ditolak') {
                                                        $statusColor = 'red';
                                                    }
                                                @endphp
                                                <div
                                                    class="flex items-center justify-end gap-2 text-{{ $statusColor }}-600">
                                                    <div
                                                        class="w-1.5 h-1.5 bg-{{ $statusColor }}-600 rounded-full {{ $row['status_pendaftaran'] === 'pending' ? 'animate-pulse' : '' }}">
                                                    </div>
                                                    <span
                                                        class="text-[10px] font-black uppercase tracking-widest">{{ $row['status_pendaftaran'] }}</span>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="px-8 py-10 text-center">
                                                <p class="text-xs font-bold text-slate-400">Tidak ada data untuk
                                                    ditampilkan
                                                </p>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="p-8 bg-slate-50/30 mt-auto border-t border-slate-50 flex items-center justify-center">
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest italic">-- Gunakan
                                tombol
                                'Generate Preview' untuk memuat data sesuai filter --</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script>
            function handleReportAction(action) {
                const type = document.getElementById('report_type').value;
                const event = document.getElementById('event_uid').value;
                const role = '{{ $user['nama_role'] }}';

                if (type === 'buku_acara') {
                    const url = `/${role}/dashboard/management-event/${event}/export-buku-acara`;
                    window.open(url, '_blank');
                    return;
                }

                if (type === 'buku_hasil') {
                    if (event === 'all') {
                        alert('Silakan pilih salah satu event spesifik untuk mencetak Buku Hasil.');
                        return;
                    }
                    const url = `/${role}/dashboard/management-event/${event}/export-buku-hasil`;
                    window.open(url, '_blank');
                    return;
                }

                if (action === 'preview') {
                    const url = `/${role}/dashboard/export-reports?event_uid=${event}`;
                    window.location.href = url;
                } else {
                    const url = `/${role}/dashboard/export-reports/process?type=${type}&format=${action}&event_uid=${event}`;
                    window.open(url, '_blank');
                }
            }
        </script>
    @else
        @include('layouts.layout-partials.coming-soon')
    @endif
@endsection
