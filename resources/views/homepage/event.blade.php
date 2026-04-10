@extends('layouts.layout-homepage.app')

<style>
    /* Custom Scrollbar */
    ::-webkit-scrollbar {
        width: 6px;
    }
    ::-webkit-scrollbar-track {
        background: #f1f5f9;
    }
    ::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 10px;
    }

    .event-card {
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .event-card:hover {
        transform: translateY(-12px);
    }
    .glass-effect {
        background: rgba(255, 255, 255, 0.7);
        backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.3);
    }
    .text-gradient {
        background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }
    .bg-grid {
        background-image: radial-gradient(#e2e8f0 1px, transparent 1px);
        background-size: 30px 30px;
    }
</style>

@section('homepage-section')
    {{-- Header Section --}}
    <section class="relative pt-40 pb-24 bg-slate-950 overflow-hidden">
        <div class="absolute inset-0 bg-grid opacity-10"></div>
        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-full h-full bg-gradient-to-b from-blue-600/20 to-transparent blur-3xl"></div>
        
        <div class="container mx-auto px-6 relative z-10 text-center">
            <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-blue-500/10 border border-blue-500/20 text-blue-400 text-xs font-bold uppercase tracking-widest mb-8 animate-fade-in">
                <span class="relative flex h-2 w-2">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-blue-500"></span>
                </span>
                Arena Kompetisi KSC
            </div>
            <h1 class="text-5xl md:text-7xl font-black text-white mb-8 leading-tight">
                Temukan <span class="text-blue-500 italic">Passion</span> & <br>
                Raih <span class="relative">
                    <span class="relative z-10 text-yellow-400">Podium Juara</span>
                    <svg class="absolute -bottom-2 left-0 w-full" viewBox="0 0 318 12" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M1 10C55.5 4 115 1.5 174 1.5C233 1.5 292.5 4 317 10" stroke="#FACC15" stroke-width="3" stroke-linecap="round"/></svg>
                </span>
            </h1>
            <p class="text-slate-400 text-lg max-w-2xl mx-auto mb-12 leading-relaxed">
                Jelajahi berbagai kompetisi renang bergengsi. Dari tingkat internal hingga terbuka, saatnya buktikan hasil latihanmu di sini.
            </p>

            {{-- Search Bar --}}
            <div class="relative max-w-3xl mx-auto group">
                <div class="absolute -inset-1 bg-gradient-to-r from-blue-600 to-cyan-500 rounded-[2.5rem] blur opacity-25 group-hover:opacity-50 transition duration-1000"></div>
                <div class="relative flex items-center bg-slate-900 border border-slate-800 rounded-[2.5rem] p-2">
                    <div class="pl-6 pr-4">
                        <i data-lucide="search" class="w-6 h-6 text-slate-500"></i>
                    </div>
                    <input type="text" id="eventSearchInput" value="{{ $currentKeyword ?? '' }}" placeholder="Cari nama event atau lokasi kompetisi..."
                        class="w-full bg-transparent border-none text-white text-sm font-medium focus:ring-0 placeholder:text-slate-600 py-4">
                    <button class="bg-blue-600 hover:bg-blue-500 text-white px-8 py-4 rounded-[2rem] font-bold text-sm transition-all shadow-lg shadow-blue-600/20 mr-1">
                        Cari Sekarang
                    </button>
                </div>
            </div>
        </div>
    </section>

    {{-- Events Grid --}}
    <section class="py-32 bg-white relative">
        <div class="container mx-auto px-6">
            <div class="grid lg:grid-cols-3 md:grid-cols-2 gap-10">
                @forelse ($events['data'] as $event)
                    <div class="event-card group bg-white rounded-[3rem] border border-slate-100 shadow-xl shadow-slate-200/40 overflow-hidden flex flex-col h-full ring-1 ring-slate-100">
                        {{-- Banner Area --}}
                        <div class="relative h-64 overflow-hidden">
                            @if ($event->banner_event)
                                <img src="{{ url('/file/banner-event/' . $event['banner_event']) }}"
                                    class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                            @else
                                <div class="w-full h-full bg-gradient-to-br from-slate-100 to-slate-200 flex items-center justify-center">
                                    <i data-lucide="image" class="w-12 h-12 text-slate-300"></i>
                                </div>
                            @endif

                            {{-- Badges --}}
                            <div class="absolute top-6 left-6 flex flex-col gap-2">
                                <span class="px-4 py-1.5 glass-effect rounded-full text-[10px] font-black uppercase tracking-widest text-slate-900 shadow-sm">
                                    {{ $event['eventCategories'][0]['category']['nama_kategori'] ?? 'Swimming' }}
                                </span>
                                @if($event['status_event'] === 'berjalan')
                                    <span class="px-4 py-1.5 bg-emerald-500 text-white rounded-full text-[10px] font-black uppercase tracking-widest shadow-lg shadow-emerald-500/30">
                                        Open Registration
                                    </span>
                                @else
                                    <span class="px-4 py-1.5 bg-slate-800 text-white rounded-full text-[10px] font-black uppercase tracking-widest">
                                        {{ $event['status_event'] }}
                                    </span>
                                @endif
                            </div>

                            {{-- Cost Badge --}}
                            <div class="absolute bottom-6 right-6">
                                <div class="px-5 py-2 {{ $event['biaya_event'] > 0 ? 'bg-white' : 'bg-ksc-accent' }} rounded-2xl shadow-xl flex items-center gap-2 border border-white/50">
                                    <span class="text-[10px] font-black uppercase {{ $event['biaya_event'] > 0 ? 'text-slate-400' : 'text-slate-900' }}">
                                        {{ $event['biaya_event'] > 0 ? 'Investasi' : 'Free' }}
                                    </span>
                                    @if($event['biaya_event'] > 0)
                                        <span class="text-sm font-black text-slate-900 italic">Rp{{ number_format($event['biaya_event'], 0, ',', '.') }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Content Area --}}
                        <div class="p-10 flex-grow flex flex-col">
                            <div class="flex items-center gap-3 mb-6 text-slate-400">
                                <div class="flex items-center gap-1.5">
                                    <i data-lucide="calendar" class="w-4 h-4 text-blue-500"></i>
                                    <span class="text-[11px] font-bold uppercase tracking-wider">
                                        {{ \Carbon\Carbon::parse($event['tanggal_mulai'])->translatedFormat('d M Y') }}
                                    </span>
                                </div>
                                <div class="w-1 h-1 bg-slate-300 rounded-full"></div>
                                <div class="flex items-center gap-1.5">
                                    <i data-lucide="map-pin" class="w-4 h-4 text-rose-500"></i>
                                    <span class="text-[11px] font-bold uppercase tracking-wider truncate max-w-[120px]">
                                        {{ $event['lokasi_event'] }}
                                    </span>
                                </div>
                            </div>

                            <h3 class="text-2xl font-black text-slate-900 leading-tight mb-6 group-hover:text-blue-600 transition-colors line-clamp-2 italic uppercase">
                                {{ $event['nama_event'] }}
                            </h3>

                            {{-- Features / Info --}}
                            <div class="flex flex-wrap gap-4 mb-10 mt-auto">
                                <div class="flex items-center gap-2 bg-slate-50 px-4 py-2 rounded-xl border border-slate-100">
                                    <i data-lucide="layout" class="w-4 h-4 text-blue-500"></i>
                                    <span class="text-[10px] font-bold text-slate-600 uppercase">{{ $event['jumlah_lintasan'] ?? 8 }} LINTASAN</span>
                                </div>
                                <div class="flex items-center gap-2 bg-slate-50 px-4 py-2 rounded-xl border border-slate-100">
                                    <i data-lucide="users" class="w-4 h-4 text-blue-500"></i>
                                    <span class="text-[10px] font-bold text-slate-600 uppercase">{{ $event['registrations_count'] }} PESERTA</span>
                                </div>
                            </div>

                            <a href="{{ url('/detail-event/' . $event['slug'] . '/' . $event['uid']) }}"
                                class="w-full py-5 bg-slate-900 hover:bg-blue-600 text-white rounded-2xl font-black text-xs uppercase tracking-[0.2em] transition-all flex items-center justify-center gap-3 group/btn shadow-xl shadow-slate-200 hover:shadow-blue-500/30">
                                Lihat Detail <i data-lucide="arrow-right" class="w-5 h-5 group-hover/btn:translate-x-1 transition-transform"></i>
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="lg:col-span-3 py-20 text-center">
                        <div class="w-24 h-24 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-6">
                            <i data-lucide="calendar-x" class="w-10 h-10 text-slate-300"></i>
                        </div>
                        <h3 class="text-xl font-bold text-slate-400 uppercase tracking-widest">Belum Ada Event Ditemukan</h3>
                    </div>
                @endforelse
            </div>

            {{-- Pagination --}}
            @if ($events['last_page'] > 1)
                @php
                    $paginationPath = ($currentKeyword ?? null) ? '/events/search/' . urlencode($currentKeyword) . '/page/' : '/events/page/';
                @endphp
                <div class="mt-24 flex items-center justify-center gap-4">
                    @if ($events['current_page'] > 1)
                        <a href="{{ url($paginationPath . ($events['current_page'] - 1)) }}"
                            class="w-14 h-14 bg-white border border-slate-200 text-slate-600 rounded-2xl flex items-center justify-center hover:bg-slate-50 transition shadow-sm">
                            <i data-lucide="chevron-left" class="w-6 h-6"></i>
                        </a>
                    @endif

                    <div class="flex items-center gap-2 bg-slate-50 p-2 rounded-2xl border border-slate-100">
                        @for ($i = 1; $i <= $events['last_page']; $i++)
                            <a href="{{ url($paginationPath . $i) }}"
                                class="w-10 h-10 rounded-xl flex items-center justify-center text-sm font-black transition-all {{ $events['current_page'] == $i ? 'bg-blue-600 text-white shadow-lg shadow-blue-500/30' : 'text-slate-400 hover:text-slate-900' }}">
                                {{ $i }}
                            </a>
                        @endfor
                    </div>

                    @if ($events['current_page'] < $events['last_page'])
                        <a href="{{ url($paginationPath . ($events['current_page'] + 1)) }}"
                            class="w-14 h-14 bg-white border border-slate-200 text-slate-600 rounded-2xl flex items-center justify-center hover:bg-slate-50 transition shadow-sm">
                            <i data-lucide="chevron-right" class="w-6 h-6"></i>
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </section>

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const searchInput = document.getElementById('eventSearchInput');
            if (searchInput) {
                searchInput.addEventListener('keypress', function(e) {
                    if (e.key === 'Enter') {
                        const term = e.target.value.trim();
                        window.location.href = term !== "" ? `/events/search/${encodeURIComponent(term)}` : `/events`;
                    }
                });
            }
            if (typeof lucide !== 'undefined') lucide.createIcons();
        });
    </script>
@endsection
