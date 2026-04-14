@extends('layouts.layout-dashboard.app')

@section('dashboard-section')
<div class="p-4 md:p-8">
    <div class="mb-6 flex items-center gap-4">
        <a href="{{ url('/' . $user['nama_role'] . '/dashboard/management-result') }}" class="p-2 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 transition shadow-sm">
            <i data-lucide="chevron-left" class="w-5 h-5 text-slate-600"></i>
        </a>
        <div>
            <h2 class="text-2xl font-bold text-slate-900 leading-tight">Pilih Nomor Acara</h2>
            <p class="text-sm text-slate-500">{{ $event['nama_event'] }}</p>
        </div>
    </div>

    <div class="bg-white rounded-3xl border border-slate-100 shadow-xl shadow-slate-200/50 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100">
                        <th class="px-6 py-4 font-black text-[10px] text-slate-400 uppercase tracking-widest text-center">No. Acara</th>
                        <th class="px-6 py-4 font-black text-[10px] text-slate-400 uppercase tracking-widest">Nama Acara / Kategori</th>
                        <th class="px-6 py-4 font-black text-[10px] text-slate-400 uppercase tracking-widest text-center">Waktu Mulai</th>
                        <th class="px-6 py-4 font-black text-[10px] text-slate-400 uppercase tracking-widest text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @foreach($categories as $cat)
                    <tr class="hover:bg-slate-50/50 transition-colors group">
                        <td class="px-6 py-5 text-center">
                            <span class="inline-flex items-center justify-center w-10 h-10 bg-slate-100 text-slate-900 font-black rounded-xl text-lg">
                                {{ $cat['nomor_acara'] }}
                            </span>
                        </td>
                        <td class="px-6 py-5">
                            <p class="font-bold text-slate-900 group-hover:text-ksc-blue transition uppercase">{{ $cat['nama_acara'] }}</p>
                            <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">{{ $cat['category']['kode_ku'] ?? 'UMUM' }}</span>
                        </td>
                        <td class="px-6 py-5 text-center">
                            <span class="text-sm font-bold text-slate-700">{{ date('H:i', strtotime($cat['waktu_mulai'])) }} WIB</span>
                        </td>
                        <td class="px-6 py-5">
                            <div class="flex justify-center">
                                <a href="{{ url('/' . $user['nama_role'] . '/dashboard/management-result/' . $event['uid'] . '/input/' . $cat['uid']) }}" 
                                   class="flex items-center gap-2 bg-ksc-blue hover:bg-ksc-dark text-white px-4 py-2 rounded-lg font-bold text-xs transition shadow-md active:scale-95">
                                    <i data-lucide="edit-3" class="w-4 h-4"></i>
                                    <span>Input Hasil</span>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
