@extends('layouts.layout-dashboard.app')

@section('dashboard-section')
<div class="p-4 md:p-8">
    <div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <a href="{{ url('/' . $user['nama_role'] . '/dashboard/management-result/' . $eventCategory['event']['uid'] . '/categories') }}" 
               class="p-2 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 transition shadow-sm">
                <i data-lucide="chevron-left" class="w-5 h-5 text-slate-600"></i>
            </a>
            <div>
                <h2 class="text-2xl font-bold text-slate-900 leading-tight">Input Hasil Lomba</h2>
                <p class="text-sm text-slate-500 uppercase tracking-wide font-medium">Acara {{ $eventCategory['nomor_acara'] }}: {{ $eventCategory['nama_acara'] }}</p>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ url('/' . $user['nama_role'] . '/dashboard/management-event/' . $eventCategory['event']['uid'] . '/export-buku-hasil') }}" 
               target="_blank"
               class="flex items-center gap-2 bg-blue-50 text-ksc-blue border border-blue-100 px-4 py-2.5 rounded-xl font-bold text-xs uppercase tracking-widest transition hover:bg-ksc-blue hover:text-white shadow-sm">
                <i data-lucide="award" class="w-4 h-4"></i>
                Cetak Buku Hasil
            </a>
        </div>
    </div>

    <form action="{{ url('/' . $user['nama_role'] . '/dashboard/management-result/' . $eventCategory['event']['uid'] . '/input/' . $eventCategory['uid'] . '/process') }}" 
          method="POST">
        @csrf
        <div class="bg-white rounded-3xl border border-slate-100 shadow-xl shadow-slate-200/50 overflow-hidden mb-6">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-100">
                            <th class="px-6 py-4 font-black text-[10px] text-slate-400 uppercase tracking-widest">Nama Atlet</th>
                            <th class="px-6 py-4 font-black text-[10px] text-slate-400 uppercase tracking-widest">Klub</th>
                            <th class="px-6 py-4 font-black text-[10px] text-slate-400 uppercase tracking-widest text-center" width="200">Waktu (MM:SS.ss)</th>
                            <th class="px-6 py-4 font-black text-[10px] text-slate-400 uppercase tracking-widest text-center" width="180">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @forelse($athletes as $atlet)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-6 py-4">
                                <p class="font-bold text-slate-900 uppercase text-xs">{{ $atlet['nama_lengkap'] }}</p>
                                <p class="text-[9px] text-slate-400 font-bold uppercase tracking-tighter">ID: {{ $atlet['uid'] }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-xs font-medium text-slate-600 uppercase">{{ $atlet['klub_renang'] ?: '-' }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-1">
                                    <input type="number" 
                                           name="results[{{ $atlet['uid'] }}][min]" 
                                           value="{{ $atlet['waktu_akhir'] ? explode(':', $atlet['waktu_akhir'])[0] : '00' }}"
                                           placeholder="MM"
                                           min="0" max="59"
                                           class="w-14 bg-slate-50 border border-slate-200 text-slate-900 text-center font-mono font-bold text-sm rounded-lg focus:ring-2 focus:ring-ksc-blue outline-none p-2 transition"
                                           title="Menit">
                                    <span class="font-bold">:</span>
                                    <input type="number" 
                                           name="results[{{ $atlet['uid'] }}][sec]" 
                                           value="{{ $atlet['waktu_akhir'] ? explode('.', explode(':', $atlet['waktu_akhir'])[1] ?? '00.00')[0] : '00' }}"
                                           placeholder="SS"
                                           min="0" max="59"
                                           class="w-14 bg-slate-50 border border-slate-200 text-slate-900 text-center font-mono font-bold text-sm rounded-lg focus:ring-2 focus:ring-ksc-blue outline-none p-2 transition"
                                           title="Detik">
                                    <span class="font-bold">.</span>
                                    <input type="number" 
                                           name="results[{{ $atlet['uid'] }}][ms]" 
                                           value="{{ $atlet['waktu_akhir'] ? explode('.', $atlet['waktu_akhir'])[1] ?? '00' : '00' }}"
                                           placeholder="ms"
                                           min="0" max="99"
                                           class="w-14 bg-slate-50 border border-slate-200 text-slate-900 text-center font-mono font-bold text-sm rounded-lg focus:ring-2 focus:ring-ksc-blue outline-none p-2 transition"
                                           title="Milidetik">
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <select name="results[{{ $atlet['uid'] }}][status]" 
                                        class="w-full bg-slate-50 border border-slate-200 text-slate-900 text-xs font-bold rounded-xl focus:ring-2 focus:ring-ksc-blue outline-none p-2.5 transition">
                                    <option value="FINISH" {{ $atlet['status'] == 'FINISH' ? 'selected' : '' }}>FINISH</option>
                                    <option value="NS" {{ $atlet['status'] == 'NS' ? 'selected' : '' }}>NO SWIM (NS)</option>
                                    <option value="DSQ" {{ $atlet['status'] == 'DSQ' ? 'selected' : '' }}>DISKUALIFIKASI (DSQ)</option>
                                </select>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-slate-400 font-medium italic">
                                Belum ada atlet yang terdaftar atau diterima di nomor acara ini.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3 pb-10">
            <a href="{{ url('/' . $user['nama_role'] . '/dashboard/management-result/' . $eventCategory['event']['uid'] . '/categories') }}" 
               class="px-8 py-3 bg-white border border-slate-200 text-slate-600 rounded-2xl font-bold text-sm hover:bg-slate-50 transition shadow-sm">
                Batal
            </a>
            <button type="submit" 
                    class="px-10 py-3 bg-slate-900 hover:bg-black text-white rounded-2xl font-bold text-sm transition shadow-xl shadow-slate-200 flex items-center gap-2">
                <i data-lucide="save" class="w-4 h-4"></i>
                <span>Simpan Hasil Lomba</span>
            </button>
        </div>
    </form>
</div>
@endsection
