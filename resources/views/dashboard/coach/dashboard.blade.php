@extends('layouts.layout-dashboard.app')
@section('dashboard-section')
    <div class="flex-1 overflow-y-auto p-8">
        <div class="max-w-7xl mx-auto space-y-8">
            <!-- Header Title -->
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-black text-slate-900 tracking-tight">DASHBOARD COACH</h1>
                    <p class="text-sm text-slate-500 font-medium leading-relaxed">Selamat datang kembali, Coach {{ $user['nama_lengkap'] }}. Pantau perkembangan atlet hari ini.</p>
                </div>
                <div class="flex items-center gap-3">
                    <span class="px-4 py-2 bg-white border border-slate-100 rounded-xl text-[10px] font-bold text-slate-400 uppercase tracking-widest shadow-sm">
                        {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}
                    </span>
                </div>
            </div>

            <!-- Stats Grid -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6">
                <div class="bg-white p-4 md:p-6 rounded-[2rem] border border-slate-100 shadow-sm transition hover:shadow-md">
                    <div class="h-10 w-10 bg-blue-50 text-ksc-blue rounded-xl flex items-center justify-center mb-4">
                        <i data-lucide="users" class="w-5 h-5"></i>
                    </div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Total Member</p>
                    <h3 class="text-xl md:text-2xl font-black text-slate-900 mt-1">{{ number_format($totalAnggota) }}</h3>
                </div>

                <div class="bg-white p-4 md:p-6 rounded-[2rem] border border-slate-100 shadow-sm transition hover:shadow-md">
                    <div class="h-10 w-10 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center mb-4">
                        <i data-lucide="calendar" class="w-5 h-5"></i>
                    </div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Event Berjalan</p>
                    <h3 class="text-xl md:text-2xl font-black text-slate-900 mt-1">{{ number_format($eventAktif) }}</h3>
                </div>

                <div class="bg-white p-4 md:p-6 rounded-[2rem] border border-slate-100 shadow-sm transition hover:shadow-md">
                    <div class="h-10 w-10 bg-amber-50 text-amber-600 rounded-xl flex items-center justify-center mb-4">
                        <i data-lucide="clipboard-check" class="w-5 h-5"></i>
                    </div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Antrean Validasi</p>
                    <h3 class="text-xl md:text-2xl font-black text-slate-900 mt-1">{{ number_format($antreanValidasi) }}</h3>
                </div>

                <div class="bg-white p-4 md:p-6 rounded-[2rem] border border-slate-100 shadow-sm transition hover:shadow-md">
                    <div class="h-10 w-10 bg-purple-50 text-purple-600 rounded-xl flex items-center justify-center mb-4">
                        <i data-lucide="award" class="w-5 h-5"></i>
                    </div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Pesan Unread</p>
                    <h3 class="text-xl md:text-2xl font-black text-slate-900 mt-1">{{ number_format($totalUnreadNotification) }}</h3>
                </div>
            </div>

            <div class="grid lg:grid-cols-3 gap-8">
                <!-- Upcoming Competitions -->
                <div class="lg:col-span-2 space-y-6">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-1.5 h-6 bg-ksc-blue rounded-full"></div>
                            <h2 class="text-xl font-bold text-slate-900">Jadwal Event Terdekat</h2>
                        </div>
                        <a href="{{ url('/' . $user['nama_role'] . '/dashboard/management-event') }}" class="text-xs font-bold text-ksc-blue hover:underline bg-blue-50 px-4 py-2 rounded-lg">Lihat Semua</a>
                    </div>
                    
                    <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm overflow-hidden">
                        <div class="p-6 md:p-8 space-y-4">
                            @forelse($upcomingEvents as $ev)
                            <div class="flex items-center gap-4 md:gap-6 p-4 rounded-3xl bg-slate-50 hover:bg-white hover:shadow-xl hover:shadow-slate-100 transition group border border-transparent hover:border-slate-100">
                                <div class="h-12 w-12 md:h-16 md:w-16 rounded-2xl bg-ksc-blue flex flex-col items-center justify-center text-white shrink-0 shadow-lg shadow-blue-100 group-hover:scale-110 transition-transform">
                                    <span class="text-sm md:text-lg font-black leading-none">{{ \Carbon\Carbon::parse($ev['tanggal_event'])->format('d') }}</span>
                                    <span class="text-[8px] md:text-[10px] font-bold uppercase tracking-tighter">{{ \Carbon\Carbon::parse($ev['tanggal_event'])->translatedFormat('M') }}</span>
                                </div>
                                <div class="flex-1">
                                    <h4 class="text-sm md:text-base font-black text-slate-900 leading-tight tracking-tight">{{ $ev['nama_event'] }}</h4>
                                    <div class="flex items-center gap-3 mt-1">
                                        <div class="flex items-center gap-1 text-slate-400">
                                            <i data-lucide="map-pin" class="w-3 h-3"></i>
                                            <span class="text-[10px] md:text-xs font-medium">{{ $ev['lokasi_event'] }}</span>
                                        </div>
                                        <div class="w-1 h-1 bg-slate-300 rounded-full"></div>
                                        <div class="flex items-center gap-1 text-slate-400">
                                            <i data-lucide="tag" class="w-3 h-3"></i>
                                            <span class="text-[10px] md:text-xs font-medium">Rp {{ number_format((float)($ev['biaya_event'] ?? 0)) }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <span class="px-3 py-1 bg-emerald-100 text-emerald-600 text-[8px] md:text-[10px] font-black rounded-full uppercase tracking-widest">
                                        {{ $ev['status_event'] }}
                                    </span>
                                </div>
                            </div>
                            @empty
                            <div class="py-10 text-center">
                                <img src="{{ url('assets/img/empty-box.png') }}" class="w-16 mx-auto opacity-20 filter grayscale">
                                <p class="text-xs font-bold text-slate-400 mt-4">Belum ada event berjalan</p>
                            </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Recent Activities -->
                <div class="space-y-6">
                    <div class="flex items-center gap-3">
                        <div class="w-1.5 h-6 bg-slate-900 rounded-full"></div>
                        <h2 class="text-xl font-bold text-slate-900">Aktivitas Terbaru</h2>
                    </div>
                    
                    <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm p-4 md:p-6 space-y-4">
                        @forelse($registrasiTerbaru as $reg)
                        <div class="flex items-start gap-4 p-3 rounded-2xl hover:bg-slate-50 transition border border-transparent hover:border-slate-100 group">
                            <div class="relative">
                                <img src="{{ $reg['foto_profil'] ? url('/file/users/' . $reg['foto_profil']) : 'https://ui-avatars.com/api/?name=' . urlencode($reg['nama_lengkap']) . '&background=random' }}" 
                                     class="h-10 w-10 rounded-xl object-cover shadow-md group-hover:scale-105 transition-transform">
                                <div class="absolute -bottom-1 -right-1 w-4 h-4 bg-white rounded-full flex items-center justify-center shadow-sm">
                                    <div class="w-2.5 h-2.5 rounded-full bg-blue-500"></div>
                                </div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h4 class="text-[13px] font-black text-slate-900 truncate leading-none">{{ $reg['nama_lengkap'] }}</h4>
                                <p class="text-[11px] text-slate-500 mt-1 line-clamp-1 italic font-medium">Mendaftar ke {{ $reg['nama_event'] }}</p>
                                <p class="text-[9px] text-slate-400 mt-1 uppercase font-black tracking-widest">{{ \Carbon\Carbon::parse($reg['created_at'])->diffForHumans() }}</p>
                            </div>
                        </div>
                        @empty
                        <div class="py-10 text-center">
                            <p class="text-xs font-bold text-slate-400">Belum ada aktivitas baru</p>
                        </div>
                        @endforelse
                        
                        <a href="{{ url('/' . $user['nama_role'] . '/dashboard/management-member') }}" 
                           class="block w-full py-4 bg-slate-900 hover:bg-black text-white text-center rounded-2xl font-black text-[10px] uppercase tracking-[0.2em] transition-all shadow-lg shadow-slate-200">
                            Kelola Member
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
