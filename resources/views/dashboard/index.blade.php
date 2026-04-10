@extends('layouts.layout-dashboard.app')

@section('dashboard-section')
<div class="p-4 md:p-8 space-y-8">
    {{-- 1. Bagian Welcome --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-900">Selamat Datang Kembali, {{ $user->nama_lengkap }}! 👋</h2>
            <p class="text-slate-500 text-sm mt-1">Hari ini adalah {{ date('l, d F Y') }}. Pantau aktivitas Anda di sini.</p>
        </div>
    </div>

    {{-- 2. Grid Statistik (Dinamis Berdasarkan Role) --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6">
        @if($user->can('manage-users'))
        {{-- Card Statistik Admin/Superadmin --}}
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm hover:shadow-md transition stats-card">
            <div class="flex items-center justify-between mb-4">
                <div class="p-3 bg-blue-50 rounded-xl">
                    <i data-lucide="users" class="w-6 h-6 text-ksc-blue"></i>
                </div>
                <span class="text-xs font-bold text-slate-400 uppercase">Total User</span>
            </div>
            <h3 class="text-3xl font-bold text-slate-900">{{ $totalAnggota ?? 0 }}</h3>
        </div>
        @endif

        @if($user->can('manage-events') || $user->can('register-event'))
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm hover:shadow-md transition stats-card">
            <div class="flex items-center justify-between mb-4">
                <div class="p-3 bg-orange-50 rounded-xl">
                    <i data-lucide="trophy" class="w-6 h-6 text-orange-500"></i>
                </div>
                <span class="text-xs font-bold text-slate-400 uppercase">Event Aktif</span>
            </div>
            <h3 class="text-3xl font-bold text-slate-900">{{ $eventAktif ?? count($events ?? []) }}</h3>
        </div>
        @endif

        @if($user->can('manage-events'))
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm hover:shadow-md transition stats-card">
            <div class="flex items-center justify-between mb-4">
                <div class="p-3 bg-green-50 rounded-xl">
                    <i data-lucide="mail-check" class="w-6 h-6 text-green-500"></i>
                </div>
                <span class="text-xs font-bold text-slate-400 uppercase">Antrean Validasi</span>
            </div>
            <h3 class="text-3xl font-bold text-slate-900">{{ $antreanValidasi ?? 0 }}</h3>
        </div>
        @endif
        
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm hover:shadow-md transition stats-card">
            <div class="flex items-center justify-between mb-4">
                <div class="p-3 bg-purple-50 rounded-xl">
                    <i data-lucide="bell" class="w-6 h-6 text-purple-500"></i>
                </div>
                <span class="text-xs font-bold text-slate-400 uppercase">Notifikasi</span>
            </div>
            <h3 class="text-3xl font-bold text-slate-900">{{ $totalUnreadNotification }}</h3>
        </div>
    </div>

    {{-- 3. Konten Spesifik Role --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- List Event (Kiri) --}}
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="font-bold text-slate-900 flex items-center gap-2">
                        <i data-lucide="calendar" class="w-5 h-5 text-ksc-blue"></i>
                        Event Terkini
                    </h3>
                    <a href="{{ url('/' . $user->nama_role . '/dashboard/event') }}" class="text-sm text-ksc-blue font-bold hover:underline">Lihat Semua</a>
                </div>
                <div class="p-6 space-y-4">
                    @forelse($events ?? $upcomingEvents ?? [] as $event)
                    <div class="flex items-center gap-4 p-4 rounded-xl border border-slate-100 hover:bg-slate-50 transition">
                        <div class="w-12 h-12 bg-slate-100 rounded-lg flex items-center justify-center flex-shrink-0">
                            <i data-lucide="package" class="w-6 h-6 text-slate-400"></i>
                        </div>
                        <div class="flex-grow">
                            <h4 class="font-bold text-slate-900">{{ $event['nama_event'] }}</h4>
                            <p class="text-xs text-slate-500">{{ $event['lokasi_event'] }} | {{ date('d M Y', strtotime($event['tanggal_mulai'] ?? $event['created_at'])) }}</p>
                        </div>
                        <span class="px-3 py-1 bg-green-100 text-green-700 text-[10px] font-bold rounded-full uppercase">{{ $event['status_event'] }}</span>
                    </div>
                    @empty
                    <div class="text-center py-10">
                        <p class="text-slate-400 text-sm">Belum ada event yang tersedia.</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Aktivitas / Anggota Terbaru (Kanan) --}}
        <div class="space-y-6">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="p-6 border-b border-slate-100">
                    <h3 class="font-bold text-slate-900 flex items-center gap-2">
                        <i data-lucide="user-plus" class="w-5 h-5 text-ksc-blue"></i>
                        {{ $user->can('manage-users') ? 'User Terbaru' : 'Pelatih KSC' }}
                    </h3>
                </div>
                <div class="p-6 space-y-4">
                    @forelse($members ?? $coaches ?? [] as $person)
                    <div class="flex items-center gap-3">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($person['nama_lengkap'] ?? 'User') }}&background=f1f5f9&color=64748b" class="w-10 h-10 rounded-full border border-slate-100">
                        <div>
                            <p class="text-sm font-bold text-slate-900">{{ $person['nama_lengkap'] ?? 'User' }}</p>
                            <p class="text-[10px] text-slate-500">{{ $person['nama_klub'] ?? 'Khafid Swimming Club' }}</p>
                        </div>
                    </div>
                    @empty
                    <p class="text-center text-slate-400 text-xs py-4">Data tidak tersedia.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
