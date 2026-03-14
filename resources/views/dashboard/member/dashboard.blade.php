@extends('layouts.layout-dashboard.app')

@section('dashboard-section')
    <div class="flex-1 overflow-y-auto p-4 md:p-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            <div class="lg:col-span-2 space-y-12">

                {{-- Bagian Kompetisi --}}
                <div class="space-y-6">
                    <div class="flex items-center justify-between">
                        <h2 class="text-xl font-bold text-slate-900">Kompetisi Terdekat</h2>
                        <a href="{{ url('/events') }}" class="text-sm font-bold text-ksc-blue hover:underline">Lihat Semua</a>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @forelse ($events as $event)
                            <div
                                class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden flex flex-col text-left">
                                <div class="w-full h-48 bg-slate-100 relative">
                                    @if ($event->banner_event == null)
                                        <img src="https://lh5.googleusercontent.com/proxy/t08n2HuxPfw8OpbutGWjekHAgxfPFv-pZZ5_-uTfhEGK8B5Lp-VN4VjrdxKtr8acgJA93S14m9NdELzjafFfy13b68pQ7zzDiAmn4Xg8LvsTw1jogn_7wStYeOx7ojx5h63Gliw"
                                            class="w-full h-full object-cover">
                                    @else
                                        <img src="{{ url('/file/banner-event/' . $event['banner_event']) }}"
                                            class="w-full h-full object-cover">
                                    @endif
                                    @if (isset($event['is_registered']) && $event['is_registered'])
                                        <div
                                            class="absolute top-3 left-3 px-3 py-1 bg-green-500 text-white text-[10px] font-bold rounded-full shadow-lg">
                                            TERDAFTAR
                                        </div>
                                    @endif
                                </div>
                                <div class="flex-1 p-6">
                                    <h3 class="text-lg font-bold text-slate-900 mb-1">{{ $event['nama_event'] }}</h3>
                                    <div class="flex flex-wrap gap-3 text-[10px] font-bold text-slate-400 mb-6 uppercase">
                                        <span class="flex items-center gap-1.5"><i data-lucide="calendar"
                                                class="w-3.5 h-3.5"></i>
                                            {{ \Carbon\Carbon::parse($event['tanggal_event'])->translatedFormat('d M Y') }}</span>
                                        <span class="flex items-center gap-1.5"><i data-lucide="map-pin"
                                                class="w-3.5 h-3.5"></i> {{ $event['lokasi_event'] }}</span>
                                    </div>
                                    <a href="{{ url('/detail-event/' . ($event['slug'] . '/' . $event['uid'])) }}"
                                        class="block w-full py-3 bg-ksc-blue text-white rounded-xl text-xs font-bold hover:bg-ksc-dark transition shadow-lg shadow-ksc-blue/20 text-center uppercase tracking-wider">
                                        Lihat Detail
                                    </a>
                                </div>
                            </div>
                        @empty
                            <div
                                class="md:col-span-2 py-16 flex flex-col items-center justify-center text-center bg-white rounded-3xl border border-dashed border-slate-200">
                                <i data-lucide="calendar-off" class="w-10 h-10 text-slate-300 mb-3"></i>
                                <h3 class="text-sm font-bold text-slate-900 uppercase">Belum ada event aktif</h3>
                            </div>
                        @endforelse
                    </div>
                </div>

                {{-- Bagian Peserta Lainnya --}}
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <h2 class="text-xl font-bold text-slate-900">Teman Berlatih</h2>
                        <span
                            class="text-[10px] font-bold text-slate-400 uppercase bg-slate-100 px-3 py-1 rounded-full italic">Atlet
                            Terdaftar</span>
                    </div>

                    <div class="hidden md:block bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
                        <table class="w-full text-left">
                            <thead
                                class="bg-slate-50 border-b border-slate-100 text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                                <tr>
                                    <th class="px-6 py-4">Nama Atlet</th>
                                    <th class="px-6 py-4">Klub / Instansi</th>
                                    <th class="px-6 py-4 text-center">Status Akun</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50 text-sm">
                                @forelse ($members as $member)
                                    @if ($member['uid'] != $user['uid'])
                                        <tr class="hover:bg-slate-50/50 transition">
                                            <td class="px-6 py-4 flex items-center gap-3">
                                                <div
                                                    class="w-9 h-9 rounded-full bg-slate-200 overflow-hidden shrink-0 border border-white shadow-sm">
                                                    <img src="{{ $member['foto_profil'] ? url('/file/users/' . $member['foto_profil']) : 'https://ui-avatars.com/api/?name=' . urlencode($member['nama_lengkap']) . '&background=random' }}"
                                                        class="w-full h-full object-cover">
                                                </div>
                                                <div class="flex flex-col">
                                                    <span
                                                        class="font-bold text-slate-700">{{ $member['nama_lengkap'] }}</span>
                                                    <span class="text-[10px] text-slate-400">{{ $member['email'] }}</span>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 text-slate-500 font-medium italic">
                                                {{ $member['nama_klub'] ?? 'Independen' }}</td>
                                            <td class="px-6 py-4 text-center">
                                                <span
                                                    class="px-2.5 py-1 {{ $member['is_active'] ? 'bg-green-50 text-green-600 border-green-100' : 'bg-red-50 text-red-600 border-red-100' }} text-[9px] font-black rounded-lg border uppercase">
                                                    {{ $member['is_active'] ? 'Active' : 'Inactive' }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endif
                                @empty
                                    <tr>
                                        <td colspan="3" class="px-6 py-10 text-center text-slate-400 italic">Belum ada
                                            atlet lainnya.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="grid grid-cols-1 gap-3 md:hidden">
                        @foreach ($members as $member)
                            <div
                                class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl overflow-hidden shadow-sm">
                                        <img src="{{ $member['foto_profil'] ? url('/file/users/' . $member['foto_profil']) : 'https://ui-avatars.com/api/?name=' . urlencode($member['nama_lengkap']) }}"
                                            class="w-full h-full object-cover">
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-slate-900">{{ $member['nama_lengkap'] }}</p>
                                        <p class="text-[10px] text-slate-500 italic">
                                            {{ $member['nama_klub'] ?? 'Independen' }}</p>
                                    </div>
                                </div>
                                <div
                                    class="w-2 h-2 rounded-full {{ $member['is_active'] ? 'bg-green-500' : 'bg-red-500' }}">
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="space-y-8">

                {{-- Section Pelatih --}}
                <div class="space-y-4">
                    <h2 class="text-xl font-bold text-slate-900">Coach / Pelatih</h2>
                    <div class="bg-white rounded-3xl border border-slate-100 shadow-sm p-5 space-y-4">
                        @forelse ($coaches as $coach)
                            <div class="flex items-center gap-4 group">
                                <div
                                    class="w-12 h-12 rounded-2xl bg-slate-100 overflow-hidden shrink-0 border-2 border-slate-50 group-hover:border-ksc-blue transition-colors">
                                    <img src="{{ $coach['foto_profil'] ? url('/file/users/' . $coach['foto_profil']) : 'https://ui-avatars.com/api/?name=' . urlencode($coach['nama_lengkap']) . '&background=0F172A&color=fff' }}"
                                        class="w-full h-full object-cover">
                                </div>
                                <div class="flex-1 border-b border-slate-50 pb-3">
                                    <h4 class="text-sm font-bold text-slate-900">{{ $coach['nama_lengkap'] }}</h4>
                                    <p class="text-[10px] text-ksc-blue font-bold uppercase tracking-widest">Head Coach</p>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-4">
                                <p class="text-xs text-slate-400 italic">Data pelatih belum tersedia</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                {{-- Section Pesan --}}
                <div class="space-y-4">
                    <h2 class="text-xl font-bold text-slate-900">Pesan Masuk</h2>
                    <div class="bg-white rounded-3xl border border-slate-100 shadow-sm p-6 space-y-5">

                        @forelse ($unReadNotification as $unread)
                            <div class="flex items-start gap-3">
                                <div
                                    class="h-9 w-9 rounded-xl bg-blue-50 text-ksc-blue flex items-center justify-center shrink-0">
                                    <i data-lucide="bell-ring" class="w-4 h-4"></i>
                                </div>
                                <div>
                                    <h4 class="text-sm font-bold text-slate-900">{{ $unread['judul'] }}</h4>
                                    <p class="text-[11px] text-slate-500 line-clamp-1">lihat pesan selengkapnya ...</p>
                                </div>
                            </div>
                        @empty
                            <div class="flex flex-col items-center justify-center py-4">
                                <div class="h-12 w-12 rounded-full bg-slate-50 flex items-center justify-center mb-3">
                                    <i data-lucide="mail-open" class="w-6 h-6 text-slate-300"></i>
                                </div>
                                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Tidak ada pesan masuk
                                    terbaru</p>
                            </div>
                        @endforelse

                        <a href="{{ url('/' . $user['nama_role'] . '/dashboard/notifications') }}"
                            class="block w-full py-3 text-slate-400 hover:text-ksc-blue text-[10px] font-black transition text-center uppercase tracking-widest border-t border-slate-50 pt-5">
                            Lihat Semua Pesan
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
