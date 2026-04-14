@extends('layouts.layout-dashboard.app')

@section('dashboard-section')
<div class="p-4 md:p-8">
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-slate-900 leading-tight">Manajemen Hasil Lomba</h2>
        <p class="text-sm text-slate-500">Pilih event untuk mengelola hasil pertandingan atlet</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($events as $event)
        <div class="bg-white rounded-3xl border border-slate-100 shadow-xl shadow-slate-200/50 overflow-hidden group hover:border-ksc-blue transition-all active:scale-[0.98]">
            <div class="h-32 bg-slate-100 relative overflow-hidden">
                <img src="{{ $event['banner_event'] ? url('/file/banner-event/' . $event['banner_event']) : url('/file/dummy/dummy.webp') }}" 
                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" alt="">
                <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                <div class="absolute bottom-4 left-4">
                    <span class="px-2 py-1 bg-white/20 backdrop-blur-md text-white text-[10px] font-bold rounded-lg border border-white/30 uppercase tracking-widest">
                        {{ date('d M Y', strtotime($event['tanggal_mulai'])) }}
                    </span>
                </div>
            </div>
            <div class="p-6">
                <h3 class="font-bold text-slate-900 mb-1 truncate">{{ $event['nama_event'] }}</h3>
                <p class="text-xs text-slate-500 mb-4 line-clamp-1"><i data-lucide="map-pin" class="w-3 h-3 inline mr-1"></i>{{ $event['lokasi_event'] }}</p>
                
                <a href="{{ url('/' . $user['nama_role'] . '/dashboard/management-result/' . $event['uid'] . '/categories') }}" 
                   class="flex items-center justify-center gap-2 bg-slate-900 hover:bg-black text-white px-4 py-2.5 rounded-xl font-bold text-sm transition shadow-lg shadow-slate-200">
                    <i data-lucide="list-checks" class="w-4 h-4"></i>
                    <span>Kelola Hasil</span>
                </a>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection
