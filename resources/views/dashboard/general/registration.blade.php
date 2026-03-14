@extends('layouts.layout-dashboard.app')

@section('dashboard-section')
    <div class="p-4 md:p-8 overflow-y-auto" x-data="{ openEvent: null }">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
            <div>
                <h2 class="text-3xl font-black text-slate-900 leading-tight tracking-tight">Manajemen Pendaftaran</h2>
                <p class="text-sm text-slate-500 font-medium">Kelola pendaftar berdasarkan event yang sedang berjalan.</p>
            </div>
            <button data-modal-target="modal-tambah-registrasi" data-modal-toggle="modal-tambah-registrasi"
                class="flex items-center gap-2 bg-slate-900 hover:bg-black text-white px-6 py-3 rounded-xl font-bold transition shadow-lg shadow-slate-200">
                <i data-lucide="plus-circle" class="w-5 h-5"></i>
                <span>Pendaftaran Manual</span>
            </button>
        </div>

        {{-- LOOPING EVENT (DUMMY) --}}
        <div class="space-y-4" x-data="{ openEvent: null }">

            <div class="space-y-4">
                @foreach ($events as $event)
                    @if ($event->tipe_event === 'berbayar')
                        @if ($event->status_event === 'berjalan')
                            <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
                                <div @click="openEvent = (openEvent === {{ $event->id }} ? null : {{ $event->id }})"
                                    class="p-5 flex items-center justify-between cursor-pointer hover:bg-slate-50 transition">
                                    <div class="flex items-center gap-4">
                                        <div
                                            class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center text-blue-600 shadow-inner">
                                            <i data-lucide="calendar" class="w-6 h-6"></i>
                                        </div>
                                        <div>
                                            <div class="flex items-center gap-2">
                                                <h3 class="text-lg font-bold text-slate-900">{{ $event->nama_event }}</h3>
                                                <span
                                                    class="px-2 py-0.5 text-[10px] font-black bg-blue-600 text-white rounded-full uppercase shadow-sm">Berbayar</span>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <p
                                                    class="text-xs text-slate-400 font-bold uppercase tracking-widest text-[9px]">
                                                    {{ $event->uid }}</p>
                                                <span class="text-slate-300">•</span>
                                                <span
                                                    class="text-[10px] font-bold text-blue-600 uppercase">{{ rupiah($event->biaya_event) }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-6">
                                        <div class="hidden md:flex flex-col text-right">
                                            <span
                                                class="text-[10px] font-black text-slate-400 uppercase tracking-tighter">Okupansi
                                                Peserta</span>
                                            <div class="flex items-center gap-2 justify-end">
                                                <span
                                                    class="text-sm font-bold text-blue-700">{{ $event->registrations_count }}</span>
                                                <span class="text-xs text-slate-300">/</span>
                                                <span class="text-xs font-bold text-slate-400">{{ $event->kuota_peserta }}
                                                    Kuota</span>
                                            </div>
                                        </div>
                                        <i data-lucide="chevron-down"
                                            class="w-5 h-5 text-slate-400 transition-transform duration-300"
                                            :class="openEvent === {{ $event->id }} ? 'rotate-180' : ''"></i>
                                    </div>
                                </div>

                                <div x-show="openEvent === {{ $event->id }}" x-collapse x-cloak>
                                    <div class="overflow-x-auto border-t border-slate-100">
                                        <table class="w-full text-left border-collapse">
                                            <thead class="bg-slate-50/50">
                                                <tr>
                                                    <th
                                                        class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                                        Nama Peserta</th>
                                                    <th
                                                        class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">
                                                        Tgl Daftar</th>
                                                    <th
                                                        class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">
                                                        Status Pembayaran</th>
                                                    <th <th
                                                        class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">
                                                        Status Pendaftaran</th>
                                                    <th
                                                        class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">
                                                        Pembayaran</th>
                                                    <th
                                                        class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">
                                                        Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-slate-50">
                                                @forelse ($event->registrations as $registration)
                                                    <tr class="hover:bg-blue-50/30 transition">
                                                        <td class="px-6 py-4">
                                                            <div class="flex items-center gap-3">
                                                                @if ($registration->user->foto_profil === null)
                                                                    <div
                                                                        class="w-11 h-11 rounded-xl overflow-hidden shadow-sm border border-slate-100 bg-blue-50">
                                                                        <img src="https://ui-avatars.com/api/?name={{ urlencode($registration->user->nama_lengkap) }}&background=eff6ff&color=1e40af&bold=true"
                                                                            class="w-full h-full object-cover">
                                                                    </div>
                                                                @else
                                                                    <div
                                                                        class="w-11 h-11 rounded-xl overflow-hidden shadow-sm border border-slate-200">
                                                                        <img src="{{ url('/file/users/' . $registration->user->foto_profil) }}"
                                                                            class="w-full h-full object-cover">
                                                                    </div>
                                                                @endif
                                                                <div class="flex flex-col">
                                                                    <span
                                                                        class="text-sm font-bold text-slate-800">{{ $registration->user->nama_lengkap }}</span>
                                                                    <span
                                                                        class="text-[10px] text-slate-400 font-mono">{{ $registration->nomor_pendaftaran }}</span>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td class="px-6 py-4 text-center text-sm text-slate-600">
                                                            {{ \Carbon\Carbon::parse($registration->user->tanggal_registrasi)->translatedFormat('d F Y') }}
                                                        </td>
                                                        <td class="px-6 py-4 text-center">
                                                            @if ($registration->payment->status_pembayaran === null)
                                                            <span
                                                            class="px-3 py-1 text-[9px] font-black uppercase rounded-lg bg-gray-100 text-gray-700 border border-gray-200">Tidak ada Pembayaran</span>
                                                            @else
                                                                @if ($registration->payment->status_pembayaran === 'menunggu')
                                                                    <span
                                                                        class="px-3 py-1 text-[9px] font-black uppercase rounded-lg bg-yellow-100 text-yellow-700 border border-yellow-200">{{ $registration->payment->status_pembayaran }}</span>
                                                                @endif
                                                                @if ($registration->payment->status_pembayaran === 'diterima')
                                                                    <span
                                                                        class="px-3 py-1 text-[9px] font-black uppercase rounded-lg bg-green-100 text-green-700 border border-green-200">{{ $registration->payment->status_pembayaran }}</span>
                                                                @endif
                                                                @if ($registration->payment->status_pembayaran === 'ditolak')
                                                                    <span
                                                                        class="px-3 py-1 text-[9px] font-black uppercase rounded-lg bg-red-100 text-red-700 border border-red-200">{{ $registration->payment->status_pembayaran }}</span>
                                                                @endif
                                                            @endif
                                                        </td>
                                                        <td class="px-6 py-4 text-center">
                                                            @if ($registration->status === 'menunggu')
                                                                <span
                                                                    class="px-3 py-1 text-[9px] font-black uppercase rounded-lg bg-yellow-100 text-yellow-700 border border-yellow-200">{{ $registration->status }}</span>
                                                            @endif
                                                            @if ($registration->status === 'diterima')
                                                                <span
                                                                    class="px-3 py-1 text-[9px] font-black uppercase rounded-lg bg-green-100 text-green-700 border border-green-200">{{ $registration->status }}</span>
                                                            @endif
                                                            @if ($registration->status === 'ditolak')
                                                                <span
                                                                    class="px-3 py-1 text-[9px] font-black uppercase rounded-lg bg-red-100 text-red-700 border border-red-200">{{ $registration->status }}</span>
                                                            @endif
                                                        </td>
                                                        <td class="px-6 py-4 text-center">
                                                            <button type="button"
                                                                data-modal-target="modal-detail-pembayaran-{{ $registration->uid }}"
                                                                data-modal-toggle="modal-detail-pembayaran-{{ $registration->uid }}"
                                                                class="text-xs font-bold text-blue-600 hover:text-blue-800 bg-blue-50 px-3 py-1.5 rounded-lg border border-blue-100 transition-colors">
                                                                Cek Bukti
                                                            </button>
                                                        </td>
                                                        <td class="px-6 py-4 text-center">
                                                            <div class="flex items-center justify-center gap-2">
                                                                <button title="Ubah Status Pendaftaran"
                                                                    data-modal-target="modal-edit-registrasi-{{ $registration->uid }}"
                                                                    data-modal-toggle="modal-edit-registrasi-{{ $registration->uid }}"
                                                                    class="p-2 rounded-lg bg-green-50 text-green-600 border border-green-100 hover:bg-green-600 hover:text-white transition-all">
                                                                    <i data-lucide="user-check" class="w-4 h-4"></i>
                                                                </button>
                                                                <button title="Batalkan Pendaftaran"
                                                                    data-modal-target="modal-hapus-registrasi-{{ $registration->uid }}"
                                                                    data-modal-toggle="modal-hapus-registrasi-{{ $registration->uid }}"
                                                                    class="p-2 rounded-lg bg-red-50 text-red-600 border border-red-100 hover:bg-red-600 hover:text-white transition-all">
                                                                    <i data-lucide="user-x" class="w-4 h-4"></i>
                                                                </button>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="5" class="px-6 py-12 text-center">
                                                            <div class="flex flex-col items-center justify-center gap-3">
                                                                <div class="p-4 bg-slate-50 rounded-full">
                                                                    <i data-lucide="users"
                                                                        class="w-8 h-8 text-slate-300"></i>
                                                                </div>
                                                                <div class="flex flex-col gap-1">
                                                                    <p class="text-sm font-bold text-slate-500">Belum Ada
                                                                        Pendaftar</p>
                                                                    <p
                                                                        class="text-[11px] text-slate-400 uppercase tracking-widest">
                                                                        Data pendaftaran untuk event ini masih kosong</p>
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @else
                        @if ($event->status_event === 'berjalan')
                            <div class="bg-white border border-emerald-200 rounded-2xl shadow-sm overflow-hidden">
                                <div @click="openEvent = (openEvent === {{ $event->id }} ? null : {{ $event->id }})"
                                    class="p-5 flex items-center justify-between cursor-pointer hover:bg-emerald-50/30 transition">
                                    <div class="flex items-center gap-4">
                                        <div
                                            class="w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center text-emerald-600 shadow-inner">
                                            <i data-lucide="ticket" class="w-6 h-6"></i>
                                        </div>
                                        <div>
                                            <div class="flex items-center gap-2">
                                                <h3 class="text-lg font-bold text-slate-900">{{ $event->nama_event }}
                                                </h3>
                                                <span
                                                    class="px-2 py-0.5 text-[10px] font-black bg-emerald-500 text-white rounded-full uppercase shadow-sm">Gratis</span>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <p
                                                    class="text-xs text-slate-400 font-bold uppercase tracking-widest text-[9px]">
                                                    {{ $event->uid }}</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-6">
                                        <div class="hidden md:flex flex-col text-right">
                                            <span
                                                class="text-[10px] font-black text-slate-400 uppercase tracking-tighter">Okupansi
                                                Peserta</span>
                                            <div class="flex items-center gap-2 justify-end">
                                                <span
                                                    class="text-sm font-bold text-emerald-700">{{ $event->registrations_count }}</span>
                                                <span class="text-xs text-slate-300">/</span>
                                                <span class="text-xs font-bold text-slate-400">{{ $event->kuota_peserta }}
                                                    Kuota</span>
                                            </div>
                                        </div>
                                        <i data-lucide="chevron-down"
                                            class="w-5 h-5 text-slate-400 transition-transform duration-300"
                                            :class="openEvent === {{ $event->id }} ? 'rotate-180' : ''"></i>
                                    </div>
                                </div>

                                <div x-show="openEvent === {{ $event->id }}" x-collapse x-cloak>
                                    <div class="overflow-x-auto border-t border-emerald-100">
                                        <table class="w-full text-left border-collapse">
                                            <thead class="bg-slate-50/50">
                                                <tr>
                                                    <th
                                                        class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                                        Nama Peserta</th>
                                                    <th
                                                        class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">
                                                        Tgl Daftar</th>
                                                    <th
                                                        class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">
                                                        Status</th>
                                                    <th
                                                        class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">
                                                        Pembayaran</th>
                                                    <th
                                                        class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">
                                                        Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-slate-50">
                                                @forelse ($event->registrations as $registration)
                                                    <tr class="hover:bg-emerald-50/20 transition">
                                                        <td class="px-6 py-4">
                                                            <div class="flex items-center gap-3">
                                                                @if ($registration->user->foto_profil === null)
                                                                    <div
                                                                        class="w-11 h-11 rounded-xl overflow-hidden shadow-sm border border-slate-100 bg-blue-50">
                                                                        <img src="https://ui-avatars.com/api/?name={{ urlencode($registration->user->nama_lengkap) }}&background=eff6ff&color=1e40af&bold=true"
                                                                            class="w-full h-full object-cover">
                                                                    </div>
                                                                @else
                                                                    <div
                                                                        class="w-11 h-11 rounded-xl overflow-hidden shadow-sm border border-slate-200">
                                                                        <img src="{{ url('/file/users/' . $registration->user->foto_profil) }}"
                                                                            class="w-full h-full object-cover">
                                                                    </div>
                                                                @endif
                                                                <div class="flex flex-col">
                                                                    <span
                                                                        class="text-sm font-bold text-slate-800">{{ $registration->user->nama_lengkap }}</span>
                                                                    <span
                                                                        class="text-[10px] text-slate-400 font-mono">{{ $registration->nomor_pendaftaran }}</span>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td class="px-6 py-4 text-center text-sm text-slate-600">
                                                            {{ \Carbon\Carbon::parse($registration->user->tanggal_registrasi)->translatedFormat('d F Y') }}
                                                        </td>
                                                        <td class="px-6 py-4 text-center">
                                                            @if ($registration->status === 'menunggu')
                                                                <span
                                                                    class="px-3 py-1 text-[9px] font-black uppercase rounded-lg bg-yellow-100 text-yellow-700 border border-yellow-200">{{ $registration->status }}</span>
                                                            @endif
                                                            @if ($registration->status === 'diterima')
                                                                <span
                                                                    class="px-3 py-1 text-[9px] font-black uppercase rounded-lg bg-green-100 text-green-700 border border-green-200">{{ $registration->status }}</span>
                                                            @endif
                                                            @if ($registration->status === 'ditolak')
                                                                <span
                                                                    class="px-3 py-1 text-[9px] font-black uppercase rounded-lg bg-red-100 text-red-700 border border-red-200">{{ $registration->status }}</span>
                                                            @endif
                                                        </td>
                                                        <td class="px-6 py-4 text-center">
                                                            <span
                                                                class="text-[10px] font-black uppercase text-emerald-500 bg-emerald-50 px-2 py-1 rounded">Gratis</span>
                                                        </td>
                                                        <td class="px-6 py-4 text-center">
                                                            <div class="flex items-center justify-center gap-2">
                                                                <button title="Ubah Status Pendaftaran"
                                                                    data-modal-target="modal-edit-registrasi-{{ $registration->uid }}"
                                                                    data-modal-toggle="modal-edit-registrasi-{{ $registration->uid }}"
                                                                    class="p-2 rounded-lg bg-green-50 text-green-600 border border-green-100 hover:bg-green-600 hover:text-white transition-all">
                                                                    <i data-lucide="user-check" class="w-4 h-4"></i>
                                                                </button>
                                                                <button title="Batalkan Pendaftaran"
                                                                    data-modal-target="modal-hapus-registrasi-{{ $registration->uid }}"
                                                                    data-modal-toggle="modal-hapus-registrasi-{{ $registration->uid }}"
                                                                    class="p-2 rounded-lg bg-red-50 text-red-600 border border-red-100 hover:bg-red-600 hover:text-white transition-all">
                                                                    <i data-lucide="user-x" class="w-4 h-4"></i>
                                                                </button>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="5" class="px-6 py-12 text-center">
                                                            <div class="flex flex-col items-center justify-center gap-3">
                                                                <div class="p-4 bg-slate-50 rounded-full">
                                                                    <i data-lucide="users"
                                                                        class="w-8 h-8 text-slate-300"></i>
                                                                </div>
                                                                <div class="flex flex-col gap-1">
                                                                    <p class="text-sm font-bold text-slate-500">Belum Ada
                                                                        Pendaftar</p>
                                                                    <p
                                                                        class="text-[11px] text-slate-400 uppercase tracking-widest">
                                                                        Data pendaftaran untuk event ini masih kosong</p>
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endif
                    @if ($event->status_event === 'ditunda')
                        <div class="bg-white border border-amber-200 rounded-2xl shadow-sm overflow-hidden opacity-80">
                            <div class="p-5 flex items-center justify-between bg-amber-50/30">
                                <div class="flex items-center gap-4">
                                    <div
                                        class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center text-amber-600">
                                        <i data-lucide="clock" class="w-6 h-6"></i>
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-bold text-amber-800">{{ $event->nama_event }}
                                        </h3>
                                        <p class="text-xs text-amber-600/60 font-bold uppercase">{{ $event->uid }}
                                        </p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-4">
                                    <span
                                        class="px-3 py-1 text-[10px] font-black text-amber-700 bg-amber-100 border border-amber-200 rounded-lg uppercase tracking-wider">Ditunda</span>
                                    <i data-lucide="pause-circle" class="w-5 h-5 text-amber-400"></i>
                                </div>
                            </div>
                        </div>
                    @endif
                    @if ($event->status_event === 'ditutup')
                        <div class="bg-slate-50 border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
                            <div class="p-5 flex items-center justify-between grayscale">
                                <div class="flex items-center gap-4">
                                    <div
                                        class="w-12 h-12 bg-slate-200 rounded-xl flex items-center justify-center text-slate-400">
                                        <i data-lucide="calendar-off" class="w-6 h-6"></i>
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-bold text-slate-500">{{ $event->nama_event }}</h3>
                                        <p class="text-xs text-slate-400 font-bold uppercase">{{ $event->uid }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-4">
                                    <span
                                        class="px-3 py-1 text-[10px] font-black text-slate-500 bg-slate-200 border border-slate-300 rounded-lg uppercase tracking-wider">Ditutup</span>
                                    <i data-lucide="lock" class="w-4 h-4 text-slate-400"></i>
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    </div>

    <div id="modal-tambah-registrasi" tabindex="-1" aria-hidden="true"
        class="hidden fixed top-0 left-0 right-0 z-[70] w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-full max-h-full flex items-center justify-center bg-slate-900/60 backdrop-blur-sm transition-opacity">

        <div class="relative w-full max-w-2xl max-h-full"
            x-data='{
            searchUser: "",
            selectedUser: null,
            showUserList: false,
            isPaid: false,
            users: [],
            events: [],

            init() {
                const dataRaw = @json($users);
                const eventRaw = @json($events);
                
                this.users = dataRaw.users || dataRaw;
                
                const allEvents = eventRaw.events || eventRaw;
                this.events = allEvents.filter(ev => ev.status_event === "berjalan");
            },

            get filteredUsers() {
                if (this.searchUser.trim() === "") return [];
                return this.users.filter(u => {
                    return u.nama_lengkap.toLowerCase().includes(this.searchUser.toLowerCase()) ||
                           u.email.toLowerCase().includes(this.searchUser.toLowerCase());
                });
            },

            checkEventType(e) {
                const selectedOption = e.target.options[e.target.selectedIndex];
                const tipe = selectedOption.getAttribute("data-tipe");
                // Cek apakah event berbayar
                this.isPaid = (tipe === "berbayar");
            }
        }'>

            <div class="relative bg-white rounded-2xl shadow-2xl border border-slate-200 overflow-hidden">
                {{-- Header --}}
                <div class="flex items-center justify-between p-4 md:p-6 border-b border-slate-100 bg-slate-50/50">
                    <div class="flex items-center gap-3">
                        <div
                            class="w-10 h-10 bg-blue-600 rounded-xl flex items-center justify-center text-white shadow-lg shadow-blue-200">
                            <i data-lucide="user-plus" class="w-5 h-5"></i>
                        </div>
                        <h3 class="text-xl font-bold text-slate-900 uppercase tracking-tight">Registrasi Manual</h3>
                    </div>
                    <button type="button"
                        class="text-slate-400 hover:bg-slate-100 rounded-xl w-9 h-9 flex justify-center items-center transition"
                        data-modal-hide="modal-tambah-registrasi">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>

                <form id="form-registrasi-manual" action="{{ url('/registration/event/create/process') }}" method="POST" enctype="multipart/form-data"
                    class="p-6 md:p-8 overflow-y-auto max-h-[80vh] text-left">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                        {{-- SEARCHABLE SELECT: Peserta --}}
                        <div class="md:col-span-2 relative">
                            <label class="block mb-2 text-sm font-bold text-slate-700 uppercase tracking-wider">Cari
                                Peserta</label>

                            <div class="relative">
                                <i data-lucide="search"
                                    class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400"></i>
                                <input type="text" x-model="searchUser" @focus="showUserList = true"
                                    @click.away="setTimeout(() => showUserList = false, 200)"
                                    placeholder="Ketik nama atau email peserta..."
                                    class="bg-slate-50 border border-slate-300 text-slate-900 text-sm rounded-xl focus:ring-2 focus:ring-blue-500 block w-full p-3.5 pl-12 outline-none transition">
                            </div>

                            {{-- Dropdown Result --}}
                            <div x-show="showUserList && filteredUsers.length > 0" x-transition
                                class="absolute z-50 w-full mt-2 bg-white border border-slate-200 rounded-xl shadow-xl max-h-48 overflow-y-auto">
                                <template x-for="user in filteredUsers" :key="user.uid">
                                    <div @click="selectedUser = user; searchUser = user.nama_lengkap; showUserList = false"
                                        class="px-4 py-3 hover:bg-blue-50 cursor-pointer border-b border-slate-50 last:border-none transition">
                                        <p class="text-sm font-bold text-slate-800" x-text="user.nama_lengkap"></p>
                                        <p class="text-[10px] text-slate-400 font-medium" x-text="user.email"></p>
                                    </div>
                                </template>
                            </div>

                            {{-- Hidden Input for Submit --}}
                            <input type="hidden" name="uid_user" :value="selectedUser ? selectedUser.uid : ''" required>

                            {{-- Selected Badge --}}
                            <template x-if="selectedUser">
                                <div
                                    class="mt-2 flex items-center gap-2 bg-green-50 text-green-700 px-3 py-1.5 rounded-lg w-fit border border-green-200">
                                    <i data-lucide="user-check" class="w-3 h-3"></i>
                                    <span class="text-[10px] font-bold uppercase"
                                        x-text="'Terpilih: ' + selectedUser.nama_lengkap"></span>
                                </div>
                            </template>
                        </div>

                        {{-- SELECT EVENT --}}
                        <div class="md:col-span-2">
                            <label class="block mb-2 text-sm font-bold text-slate-700 uppercase tracking-wider">Pilih
                                Event</label>
                            <select name="uid_event" @change="checkEventType($event)"
                                class="bg-slate-50 border border-slate-300 text-slate-900 text-sm rounded-xl focus:ring-2 focus:ring-blue-500 block w-full p-3.5 outline-none transition"
                                required>
                                <option value="" selected disabled>Pilih kompetisi aktif...</option>
                                <template x-for="event in events" :key="event.uid">
                                    <option :value="event.uid" :data-tipe="event.tipe_event"
                                        x-text="event.nama_event + (event.tipe_event === 'berbayar' ? ' (Berbayar)' : ' (Gratis)')">
                                    </option>
                                </template>
                            </select>
                        </div>

                        {{-- SECTION PEMBAYARAN (Conditional) --}}
                        <template x-if="isPaid">
                            <div class="md:col-span-2 mt-4 p-6 bg-amber-50 border border-amber-100 rounded-2xl space-y-5"
                                x-transition>
                                <div class="flex items-center gap-2 mb-2 text-amber-700">
                                    <i data-lucide="credit-card" class="w-4 h-4"></i>
                                    <span class="text-xs font-black uppercase tracking-widest">Informasi Pembayaran</span>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label
                                            class="block mb-2 text-[10px] font-bold text-slate-500 uppercase tracking-wider">
                                            Metode Pembayaran
                                        </label>
                                        <select name="metode_pembayaran"
                                            class="bg-white border border-slate-200 text-slate-900 text-sm rounded-xl block w-full p-3 outline-none focus:ring-2 focus:ring-blue-500 transition-all cursor-pointer">

                                            <option value="" disabled selected>Pilih metode...</option>

                                            <optgroup label="Metode Fisik">
                                                <option value="Tunai">Tunai / Cash</option>
                                            </optgroup>

                                            <optgroup label="Transfer Bank">
                                                <option value="BCA">Bank BCA</option>
                                                <option value="Mandiri">Bank Mandiri</option>
                                                <option value="BNI">Bank BNI</option>
                                                <option value="BRI">Bank BRI</option>
                                                <option value="BSI">Bank Syariah Indonesia (BSI)</option>
                                                <option value="Bank Lainnya">Bank Lainnya</option>
                                            </optgroup>

                                            <optgroup label="E-Wallet">
                                                <option value="GoPay">GoPay</option>
                                                <option value="OVO">OVO</option>
                                                <option value="Dana">Dana</option>
                                                <option value="ShopeePay">ShopeePay</option>
                                                <option value="LinkAja">LinkAja</option>
                                            </optgroup>
                                        </select>
                                    </div>
                                    <div>
                                        <label
                                            class="block mb-2 text-[10px] font-bold text-slate-500 uppercase tracking-wider">Total
                                            (IDR)</label>
                                        <input type="number" name="total_bayar"
                                            class="bg-white border border-slate-200 text-slate-900 text-sm rounded-xl block w-full p-3 outline-none"
                                            placeholder="Nominal Bayar">
                                    </div>
                                </div>
                                <div>
                                    <label
                                        class="block mb-2 text-[10px] font-bold text-slate-500 uppercase tracking-wider">Bukti
                                        Transfer (Gambar/PDF)</label>
                                    <input type="file" name="bukti_pembayaran"
                                        class="block w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-bold file:bg-slate-900 file:text-white cursor-pointer bg-white border border-slate-200 p-2 rounded-xl">
                                </div>
                            </div>
                        </template>
                    </div>

                    {{-- Footer --}}
                    <div class="flex items-center pt-8 mt-6 border-t border-slate-100 space-x-3 justify-end">
                        <button data-modal-hide="modal-tambah-registrasi" type="button"
                            class="text-slate-500 bg-white hover:bg-slate-50 rounded-xl border border-slate-200 text-sm font-bold px-8 py-3 transition">Batal</button>
                        <button type="submit"
                            class="text-white bg-blue-600 hover:bg-blue-700 font-bold rounded-xl text-sm px-10 py-3 shadow-xl shadow-blue-100 transition-all hover:-translate-y-1">Simpan
                            Registrasi</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @foreach ($events as $event)
        @foreach ($event->registrations as $registration)
            @if ($event->tipe_event === 'berbayar')
                <div id="modal-detail-pembayaran-{{ $registration->uid }}" tabindex="-1" aria-hidden="true"
                    class="hidden fixed top-0 left-0 right-0 z-[70] w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-full max-h-full flex items-center justify-center bg-slate-900/60 backdrop-blur-sm transition-opacity">
                    <div class="relative w-full max-w-lg max-h-full">
                        <div class="relative bg-white rounded-2xl shadow-2xl border border-slate-200 overflow-hidden" x-data="{ status: '{{ $registration->payment->status_pembayaran }}' }">

                            {{-- Header --}}
                            <div
                                class="flex items-center justify-between p-4 md:p-6 border-b border-slate-100 bg-slate-50/50">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-10 h-10 bg-ksc-blue rounded-xl flex items-center justify-center text-white shadow-lg shadow-ksc-blue/20">
                                        <i data-lucide="receipt" class="w-5 h-5"></i>
                                    </div>
                                    <h3 class="text-xl font-bold text-slate-900 uppercase tracking-tight">Detail Transaksi
                                        Peserta</h3>
                                </div>
                                <button type="button"
                                    class="text-slate-400 hover:bg-slate-100 hover:text-slate-900 rounded-xl text-sm w-9 h-9 flex justify-center items-center transition"
                                    data-modal-hide="modal-detail-pembayaran-{{ $registration->uid }}">
                                    <i data-lucide="x" class="w-5 h-5"></i>
                                </button>
                            </div>

                            <div class="p-6 md:p-8">
                                {{-- Detail Info (Read Only) --}}
                                <div class="grid grid-cols-2 gap-6 mb-8 text-left">
                                    {{-- <div>
                                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-1">
                                            Metode Bayar
                                        </p>
                                        <p class="text-sm font-bold text-slate-800">Transfer Bank (BCA)</p>
                                    </div> --}}
                                    {{-- <div>
                                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-1">
                                            Total Nominal
                                        </p>
                                        <p class="text-sm font-bold text-blue-600">Rp 150.000</p>
                                    </div> --}}
                                    <div class="col-span-2">
                                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-1">
                                            Waktu
                                            Transaksi</p>
                                        <p class="text-sm font-bold text-slate-800">
                                            {{ \Carbon\Carbon::parse($registration->payment->tanggal_pembayaran)->translatedFormat('d F Y H:i:s') }}
                                            WIB</p>
                                    </div>
                                    <div class="col-span-2">
                                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-2">
                                            Bukti
                                            Pembayaran</p>
                                        <div
                                            class="group relative w-full h-56 bg-slate-100 rounded-2xl border-2 border-dashed border-slate-200 flex items-center justify-center overflow-hidden transition hover:border-ksc-blue">
                                            <img src="{{ url('/file/bukti-pembayaran/' . $registration->payment->bukti_pembayaran) }}"
                                                alt="Bukti Bayar" class="w-full h-full object-contain p-2">
                                            <div
                                                class="absolute inset-0 bg-slate-900/40 opacity-0 group-hover:opacity-100 transition flex items-center justify-center">
                                                <a href="{{ url('/file/bukti-pembayaran/' . $registration->payment->bukti_pembayaran) }}"
                                                    target="_blank"
                                                    class="bg-white text-slate-900 px-4 py-2 rounded-xl text-xs font-bold flex items-center gap-2">
                                                    <i data-lucide="external-link" class="w-4 h-4"></i> Lihat Fullscreen
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <form id="form-update-pembayaran" class="space-y-5 text-left" method="POST" action="{{ url('/' . $user['nama_role'] . '/' . $user['uid'] . '/' . $registration->uid . '/dashboard/management-registration/edit/process') }}">
                                    @csrf
                                    <div>
                                        <label
                                            class="block mb-2 text-sm font-bold text-slate-700 uppercase tracking-wider">Validasi
                                            Pembayaran</label>
                                        <select name="status_pembayaran" x-model="status"
                                            class="w-full bg-slate-50 border border-slate-300 text-slate-900 text-sm rounded-xl focus:ring-2 focus:ring-ksc-blue p-3.5 outline-none transition font-bold">
                                            <option value="menunggu">Menunggu Konfirmasi</option>
                                            <option value="diterima">Terima & Verifikasi</option>
                                            <option value="ditolak">Tolak Pembayaran</option>
                                        </select>
                                    </div>

                                    <div x-show="status === 'ditolak'" x-transition class="space-y-2">
                                        <label
                                            class="block text-[10px] font-bold text-red-600 uppercase tracking-widest">Alasan
                                            Penolakan</label>
                                        <textarea name="catatan_admin" rows="3" value="{{ $registration->catatan_admin }}"
                                            class="w-full bg-red-50/30 border border-red-200 text-slate-900 text-sm rounded-xl focus:ring-2 focus:ring-red-500 p-3.5 outline-none"
                                            placeholder="Contoh: Nominal transfer kurang atau bukti buram...">{{ $registration->catatan_admin }}</textarea>
                                    </div>

                                    <div class="pt-4 border-t border-slate-100 flex gap-3">
                                        <button type="button"
                                            data-modal-hide="modal-detail-pembayaran-{{ $registration->uid }}"
                                            class="flex-1 py-3 text-sm font-bold text-slate-500 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 transition">Tutup</button>
                                        <button type="submit"
                                            class="flex-1 py-3 bg-slate-900 text-white rounded-xl text-sm font-bold hover:bg-black shadow-xl shadow-slate-200 transition transform hover:-translate-y-0.5">Simpan
                                            Perubahan</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <div id="modal-edit-registrasi-{{ $registration->uid }}" tabindex="-1" aria-hidden="true"
                class="hidden fixed top-0 left-0 right-0 z-[70] w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-full max-h-full flex items-center justify-center bg-slate-900/60 backdrop-blur-sm transition-opacity">
                <div class="relative w-full max-w-lg max-h-full">
                    <div class="relative bg-white rounded-2xl shadow-2xl border border-slate-200 overflow-hidden"
                        x-data="{ status: '{{ $registration->status }}' }">

                        {{-- Header --}}
                        <div class="flex items-center justify-between p-4 md:p-6 border-b border-slate-100 bg-slate-50/50">
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-10 h-10 bg-amber-500 rounded-xl flex items-center justify-center text-white shadow-lg shadow-amber-500/20">
                                    <i data-lucide="clipboard-check" class="w-5 h-5"></i>
                                </div>
                                <h3 class="text-xl font-bold text-slate-900 uppercase tracking-tight">Update Status Peserta
                                </h3>
                            </div>
                            <button type="button" data-modal-hide="modal-edit-registrasi-{{ $registration->uid }}"
                                class="text-slate-400 hover:bg-slate-100 hover:text-slate-900 rounded-xl text-sm w-9 h-9 flex justify-center items-center transition">
                                <i data-lucide="x" class="w-5 h-5"></i>
                            </button>
                        </div>

                        {{-- Form (Action & Method silakan diisi Junior) --}}
                        <form id="form-edit-registrasi" class="p-6 md:p-8 text-left" method="POST" action="{{ url('/' . $user['nama_role'] . '/' . $user['uid'] . '/' . $registration->uid . '/dashboard/management-registration/edit/process') }}">
                            @csrf
                            <div class="space-y-6">

                                {{-- Info Ringkas Peserta (Dummy Data) --}}
                                <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100">
                                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-3">
                                        Informasi
                                        Pendaftaran</p>
                                    <div class="space-y-2">
                                        <div class="flex justify-between text-xs">
                                            <span class="text-slate-500 font-medium">Nama Peserta:</span>
                                            <span
                                                class="text-slate-900 font-bold font-sans">{{ $registration->user->nama_lengkap }}</span>
                                        </div>
                                        <div class="flex justify-between text-xs">
                                            <span class="text-slate-500 font-medium">Nomor Registrasi:</span>
                                            <span
                                                class="text-slate-900 font-bold font-sans">{{ $registration->nomor_pendaftaran }}</span>
                                        </div>
                                        <div class="flex justify-between text-xs">
                                            <span class="text-slate-500 font-medium">Event:</span>
                                            <span
                                                class="text-slate-900 font-bold font-sans">{{ $event->nama_event }}</span>
                                        </div>
                                    </div>
                                </div>

                                {{-- Update Status --}}
                                <div>
                                    <label
                                        class="block mb-2 text-sm font-bold text-slate-700 uppercase tracking-wider">Status
                                        Pendaftaran</label>
                                    <select name="status" x-model="status"
                                        class="bg-slate-50 border border-slate-300 text-slate-900 text-sm rounded-xl focus:ring-2 focus:ring-ksc-blue block w-full p-3.5 outline-none transition font-bold">
                                        <option value="menunggu">Menunggu (Antrean)</option>
                                        <option value="diterima">Diterima (Terverifikasi)</option>
                                    </select>
                                </div>
                            </div>

                            {{-- Footer Aksi --}}
                            <div class="flex items-center pt-8 mt-6 border-t border-slate-100 space-x-3 justify-end">
                                <button data-modal-hide="modal-edit-registrasi-{{ $registration->uid }}"
                                    type="button"
                                    class="text-slate-500 bg-white hover:bg-slate-50 rounded-xl border border-slate-200 text-sm font-bold px-8 py-3 transition">Batal</button>
                                <button type="submit"
                                    class="text-white bg-slate-900 hover:bg-black font-bold rounded-xl text-sm px-10 py-3 shadow-xl transition-all hover:-translate-y-1">Simpan
                                    Perubahan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div id="modal-hapus-registrasi-{{ $registration->uid }}" tabindex="-1" aria-hidden="true"
                class="hidden fixed top-0 left-0 right-0 z-[70] w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-full max-h-full flex items-center justify-center bg-slate-900/60 backdrop-blur-sm transition-opacity">
                <div class="relative w-full max-w-sm max-h-full text-">
                    <div class="relative bg-white rounded-lg shadow-2xl border border-slate-200 text-">
                        <div class="p-4 md:p-6 text-center text-">
                            <div
                                class="w-14 h-14 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4 text-">
                                <i data-lucide="user-minus" class="text-red-600 w-8 h-8 text-"></i>
                            </div>
                            <h3 class="mb-2 text-lg font-bold text-slate-900 text-">Batalkan Pendaftaran?</h3>
                            <p class="mb-6 text-sm text-slate-500 text-">Data pendaftaran ini akan dihapus permanen dari
                                sistem.
                            </p>
                            <div class="flex justify-center gap-3">
                                <button data-modal-hide="modal-hapus-registrasi-{{ $registration->uid }}"
                                    type="button"
                                    class="text-slate-500 bg-white hover:bg-slate-100 rounded-lg border border-slate-200 text-sm font-medium px-5 py-2.5 transition text-">Tutup</button>
                                <form action="{{ url('/' . $user['nama_role'] . '/' . $user['uid'] . '/' . $registration->uid . '/dashboard/management-registration/delete/process') }}" method="POST">
                                    @csrf
                                    <button type="submit"
                                        class="text-white bg-red-600 hover:bg-red-800 font-bold rounded-lg text-sm px-5 py-2.5 transition text-">Hapus
                                        Data</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    @endforeach
@endsection
