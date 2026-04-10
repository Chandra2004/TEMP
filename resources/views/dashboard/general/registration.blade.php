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

        {{-- FLAT REGISTRATION TABLE --}}
        <div class="bg-white rounded-3xl shadow-2xl shadow-slate-200/50 border border-slate-100 overflow-hidden mb-12">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/80 border-b border-slate-100">
                            <th class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest leading-tight">Peserta / Atlet</th>
                            <th class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest leading-tight">Event & Lomba</th>
                            <th class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center leading-tight">Status Pendaftaran</th>
                            <th class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center leading-tight">Pembayaran</th>
                            <th class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right leading-tight">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @forelse ($registrations as $registration)
                            <tr class="hover:bg-blue-50/30 transition-colors group">
                                {{-- Atlet --}}
                                <td class="px-6 py-5">
                                    <div class="flex items-center gap-4 text-left">
                                        <div class="relative">
                                            <img src="{{ $registration->user->foto_profil ? url('/file/foto-profil/' . $registration->user->foto_profil) : 'https://ui-avatars.com/api/?name=' . urlencode($registration->user->nama_lengkap) . '&background=random' }}" 
                                                class="w-12 h-12 rounded-2xl object-cover shadow-sm ring-2 ring-white group-hover:ring-blue-100 transition">
                                            <div class="absolute -bottom-1 -right-1 w-4 h-4 bg-emerald-500 border-2 border-white rounded-full"></div>
                                        </div>
                                        <div>
                                            <p class="text-sm font-black text-slate-900 leading-tight">{{ $registration->user->nama_lengkap }}</p>
                                            <p class="text-[10px] text-slate-400 font-bold uppercase tracking-tight italic">{{ $registration->user->email }}</p>
                                        </div>
                                    </div>
                                </td>

                                {{-- Event --}}
                                <td class="px-6 py-5">
                                    <div class="space-y-1 text-left">
                                        <div class="flex items-center gap-2">
                                            <span class="px-2 py-0.5 bg-blue-100 text-blue-700 text-[9px] font-black rounded-lg uppercase italic">{{ $registration->eventCategory->event->nama_event }}</span>
                                        </div>
                                        <p class="text-xs font-bold text-slate-600 truncate max-w-[200px]">{{ $registration->eventCategory->category->nama_kategori }}</p>
                                        <p class="text-[9px] text-slate-400 font-bold">Lomba: {{ $registration->eventCategory->nomor_lomba }}</p>
                                        @if($registration->schedule)
                                            <div class="flex items-center gap-2 mt-1">
                                                <span class="px-1.5 py-0.5 bg-slate-900 text-white text-[8px] font-black rounded-md uppercase tracking-tighter shadow-sm">Seri: {{ $registration->schedule->nomor_seri }}</span>
                                                <span class="px-1.5 py-0.5 bg-blue-600 text-white text-[8px] font-black rounded-md uppercase tracking-tighter shadow-sm">Lint: {{ $registration->schedule->nomor_lintasan }}</span>
                                            </div>
                                        @endif
                                    </div>
                                </td>

                                {{-- Status Registrasi --}}
                                <td class="px-6 py-5 text-center">
                                    <div class="flex flex-col items-center gap-1">
                                        @if ($registration->status_pendaftaran === 'pending')
                                            <span class="px-3 py-1 bg-amber-100 text-amber-700 text-[9px] font-black rounded-xl uppercase tracking-wider">Review ACC</span>
                                        @elseif($registration->status_pendaftaran === 'diterima')
                                            <span class="px-3 py-1 bg-emerald-100 text-emerald-700 text-[9px] font-black rounded-xl uppercase tracking-wider italic">Terverifikasi</span>
                                        @else
                                            <span class="px-3 py-1 bg-red-100 text-red-700 text-[9px] font-black rounded-xl uppercase tracking-wider">Ditolak</span>
                                        @endif
                                        <p class="text-[8px] text-slate-300 font-bold">{{ \Carbon\Carbon::parse($registration->created_at)->translatedFormat('d M Y H:i') }}</p>
                                    </div>
                                </td>

                                {{-- Pembayaran --}}
                                <td class="px-6 py-5 text-center">
                                    <div class="flex flex-col items-center gap-1">
                                        @if (!$registration->payment)
                                            <span class="text-[10px] font-black text-emerald-500 uppercase italic">Bebas Biaya</span>
                                        @else
                                            @if ($registration->payment->status_pembayaran === 'selesai')
                                                <span class="flex items-center gap-1 text-emerald-600 font-black text-[10px] uppercase">
                                                    <i data-lucide="check-circle-2" class="w-3 h-3"></i> Lunas
                                                </span>
                                            @elseif($registration->payment->status_pembayaran === 'diproses' || $registration->payment->status_pembayaran === 'pending')
                                                <span class="flex items-center gap-1 text-blue-600 font-black text-[10px] uppercase">
                                                    <i data-lucide="clock" class="w-3 h-3"></i> Review Bukti
                                                </span>
                                            @elseif($registration->payment->status_pembayaran === 'gagal')
                                                <span class="flex items-center gap-1 text-red-600 font-black text-[10px] uppercase">
                                                    <i data-lucide="alert-circle" class="w-3 h-3"></i> Gagal
                                                </span>
                                            @else
                                                <span class="flex items-center gap-1 text-slate-400 font-black text-[10px] uppercase">
                                                    <i data-lucide="help-circle" class="w-3 h-3"></i> {{ $registration->payment->status_pembayaran }}
                                                </span>
                                            @endif
                                            <p class="text-[9px] font-black text-slate-900 italic">Rp{{ number_format($registration->payment->amount, 0, ',', '.') }}</p>
                                        @endif
                                    </div>
                                </td>

                                {{-- Aksi --}}
                                <td class="px-6 py-5 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <button type="button" data-modal-target="modal-review-registrasi-{{ $registration->uid }}" data-modal-toggle="modal-review-registrasi-{{ $registration->uid }}"
                                            class="p-2 bg-slate-900 text-white rounded-xl hover:bg-black transition-all shadow-lg shadow-slate-200 hover:-translate-y-1">
                                            <i data-lucide="shield-check" class="w-4 h-4 text-emerald-400"></i>
                                        </button>
                                        <button data-modal-target="modal-hapus-registrasi-{{ $registration->uid }}" data-modal-toggle="modal-hapus-registrasi-{{ $registration->uid }}"
                                            class="p-2 bg-red-50 text-red-600 rounded-xl hover:bg-red-600 hover:text-white transition-all shadow-lg shadow-red-50 hover:-translate-y-1">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-20 text-center">
                                    <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4">
                                        <i data-lucide="database-zap" class="w-10 h-10 text-slate-300"></i>
                                    </div>
                                    <h3 class="text-sm font-black text-slate-900 uppercase tracking-tighter">Pendaftar Kosong</h3>
                                    <p class="text-xs text-slate-400 mt-1 font-medium italic italic">Semua data pendaftar akan muncul di sini secara otomatis.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    <div id="modal-tambah-registrasi" tabindex="-1" aria-hidden="true"
        class="hidden fixed top-0 left-0 right-0 z-[70] w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-full max-h-full items-center justify-center bg-slate-900/60 backdrop-blur-sm transition-opacity">

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
                const uid = e.target.value;
                this.selectedEvent = this.events.find(ev => ev.uid === uid);
                // Cek apakah event berbayar
                this.isPaid = (tipe === "berbayar");
            },

            get filteredCategories() {
                if (!this.selectedEvent) return [];
                return this.selectedEvent.eventCategories || [];
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

                        {{-- SELECT CATEGORY --}}
                        <div class="md:col-span-2" x-show="selectedEvent" x-transition>
                            <label class="block mb-2 text-sm font-bold text-slate-700 uppercase tracking-wider">Pilih Kategori Lomba</label>
                            <select name="uid_event_category"
                                class="bg-slate-50 border border-slate-300 text-slate-900 text-sm rounded-xl focus:ring-2 focus:ring-blue-500 block w-full p-3.5 outline-none transition"
                                :required="selectedEvent">
                                <option value="" selected disabled>Pilih kategori...</option>
                                <template x-for="cat in filteredCategories" :key="cat.uid">
                                    <option :value="cat.uid"
                                        x-text="cat.category?.nama_kategori + ' (' + (cat.nomor_lomba || '-') + ')'">
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
                                        <select name="payment_method"
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
                                        <input type="number" name="amount"
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

    {{-- MODAL UNIFIED REVIEW (TOMBOL ACC SUPERADMIN) --}}
    @foreach ($registrations as $registration)
        <div id="modal-review-registrasi-{{ $registration->uid }}" tabindex="-1" aria-hidden="true"
            class="hidden fixed top-0 left-0 right-0 z-[70] w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-full max-h-full items-center justify-center bg-slate-900/60 backdrop-blur-sm transition-opacity">
            <div class="relative w-full max-w-4xl max-h-full">
                <div class="relative bg-white rounded-3xl shadow-2xl border border-slate-200 overflow-hidden" x-data="{ status: '{{ $registration->status_pendaftaran }}' }">
                    
                    {{-- Modal Header --}}
                    <div class="flex items-center justify-between p-6 border-b border-slate-100 bg-slate-50/50">
                        <div class="flex items-center gap-4 text-left">
                            <div class="w-12 h-12 bg-blue-600 rounded-2xl flex items-center justify-center text-white shadow-lg shadow-blue-200">
                                <i data-lucide="user-check" class="w-6 h-6"></i>
                            </div>
                            <div>
                                <h3 class="text-xl font-black text-slate-900 uppercase">Review Pendaftaran</h3>
                                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest italic">Pendaftaran: {{ $registration->nomor_pendaftaran }}</p>
                            </div>
                        </div>
                        <button type="button" class="text-slate-400 hover:bg-slate-100 rounded-xl w-10 h-10 flex justify-center items-center transition" data-modal-hide="modal-review-registrasi-{{ $registration->uid }}">
                            <i data-lucide="x" class="w-6 h-6"></i>
                        </button>
                    </div>

                    <form action="{{ url('/' . $user['nama_role'] . '/' . $user['uid'] . '/' . $registration->uid . '/dashboard/management-registration/edit/process') }}" method="POST">
                        @csrf
                        <div class="p-8 overflow-y-auto max-h-[70vh]">
                            <div class="grid grid-cols-1 lg:grid-cols-5 gap-8 text-left">
                                
                                {{-- Sisi Kiri: Info Peserta (Span 2) --}}
                                <div class="lg:col-span-2 space-y-6 border-r border-slate-100 pr-4">
                                    <div class="bg-slate-50 p-6 rounded-3xl border border-slate-100">
                                        <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-6">Profil Atlet</h4>
                                        <div class="flex items-center gap-5 mb-8">
                                            <img src="{{ $registration->user->foto_profil ? url('/file/foto-profil/' . $registration->user->foto_profil) : 'https://ui-avatars.com/api/?name=' . urlencode($registration->user->nama_lengkap) . '&background=blue&color=fff' }}" 
                                                class="w-16 h-16 rounded-2xl shadow-lg ring-4 ring-white">
                                            <div>
                                                <p class="text-lg font-black text-slate-900 leading-tight">{{ $registration->user->nama_lengkap }}</p>
                                                <p class="text-xs text-blue-600 font-bold italic">{{ $registration->user->email }}</p>
                                            </div>
                                        </div>
                                        
                                        <div class="space-y-4">
                                            <div class="p-3 bg-white rounded-2xl border border-slate-100 flex justify-between items-center shadow-sm">
                                                <span class="text-[9px] font-black text-slate-400 uppercase">Event:</span>
                                                <span class="text-[10px] font-black text-slate-900 text-right uppercase italic">{{ $registration->eventCategory->event->nama_event }}</span>
                                            </div>
                                            <div class="p-3 bg-white rounded-2xl border border-slate-100 flex justify-between items-center shadow-sm">
                                                <span class="text-[9px] font-black text-slate-400 uppercase">Lomba:</span>
                                                <p class="text-[10px] font-black text-slate-900 text-right">{{ $registration->eventCategory->category->nama_kategori }}</p>
                                            </div>
                                            <div class="p-3 bg-white rounded-2xl border border-slate-100 flex justify-between items-center shadow-sm">
                                                <span class="text-[9px] font-black text-slate-400 uppercase">Nomor:</span>
                                                <span class="text-xs font-black text-blue-600">{{ $registration->eventCategory->nomor_lomba }}</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block mb-2 text-[10px] font-black text-slate-500 uppercase tracking-widest">Keputusan Admin</label>
                                        <select name="status_pendaftaran" x-model="status" class="w-full bg-slate-900 text-white text-sm rounded-2xl border-none focus:ring-4 focus:ring-blue-500/20 p-5 font-black transition-all outline-none cursor-pointer">
                                            <option value="pending">Menunggu Verifikasi</option>
                                            <option value="diterima">Setujui Pendaftaran</option>
                                            <option value="ditolak">Tolak Pendaftaran</option>
                                        </select>
                                        <p class="text-[9px] text-slate-400 mt-2 italic px-2">*Pendaftaran yang disetujui akan otomatis melunasi invoice pendaftaran jika berbayar.</p>
                                    </div>

                                    <div x-show="status === 'ditolak'" x-transition class="animate-in slide-in-from-top duration-300">
                                        <label class="block mb-2 text-[10px] font-black text-red-600 uppercase tracking-widest">Alasan Penolakan</label>
                                        <textarea name="catatan" rows="3" class="w-full bg-red-50 border border-red-100 text-slate-900 text-sm rounded-2xl focus:ring-4 focus:ring-red-500/10 p-4 font-bold outline-none transition-all" placeholder="Tuliskan alasan penolakan agar atlet bisa memperbaiki..."></textarea>
                                    </div>
                                </div>

                                {{-- Sisi Kanan: Bukti Bayar (Span 3) --}}
                                <div class="lg:col-span-3 space-y-6 bg-slate-50/50 p-6 rounded-3xl">
                                    @if($registration->payment)
                                        <div>
                                            <div class="flex items-center justify-between mb-4">
                                                <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Bukti Pembayaran</h4>
                                                <span class="px-3 py-1 bg-white border border-slate-200 rounded-lg text-[9px] font-black text-slate-900 shadow-sm uppercase italic">
                                                    {{ $registration->payment->payment_method }} - Rp{{ number_format($registration->payment->amount, 0, ',', '.') }}
                                                </span>
                                            </div>
                                            
                                            <div class="relative group aspect-video bg-white rounded-3xl border-2 border-dashed border-slate-200 overflow-hidden flex items-center justify-center transition hover:border-blue-600 cursor-pointer shadow-inner shadow-slate-100">
                                                @if($registration->payment->bukti_pembayaran)
                                                    <img src="{{ url('/file/bukti-pembayaran/' . $registration->payment->bukti_pembayaran) }}" class="w-full h-full object-contain p-4 transition duration-500 group-hover:scale-110">
                                                    <div class="absolute inset-0 bg-slate-900/60 opacity-0 group-hover:opacity-100 transition flex items-center justify-center backdrop-blur-sm">
                                                        <a href="{{ url('/file/bukti-pembayaran/' . $registration->payment->bukti_pembayaran) }}" target="_blank" class="px-6 py-3 bg-white text-slate-900 rounded-2xl text-[10px] font-black uppercase tracking-widest shadow-2xl flex items-center gap-2">
                                                            <i data-lucide="maximize-2" class="w-4 h-4"></i> Lihat Fullscreen
                                                        </a>
                                                    </div>
                                                @else
                                                    <div class="text-center p-10">
                                                        <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4 border border-slate-100 shadow-sm">
                                                            <i data-lucide="image-off" class="w-8 h-8 text-slate-300"></i>
                                                        </div>
                                                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Atlet Belum Unggah Bukti</p>
                                                        <p class="text-[9px] text-slate-300 mt-1">Status pembayaran: {{ $registration->payment->status_pembayaran }}</p>
                                                    </div>
                                                @endif
                                            </div>
                                            
                                            <div class="mt-8 grid grid-cols-2 gap-4">
                                                <div class="p-4 bg-white rounded-2xl border border-slate-100 shadow-sm">
                                                    <p class="text-[9px] font-black text-slate-400 uppercase mb-1 italic">Waktu Bayar:</p>
                                                    <p class="text-xs font-black text-slate-900">{{ $registration->payment->tanggal_pembayaran ? \Carbon\Carbon::parse($registration->payment->tanggal_pembayaran)->translatedFormat('d F Y H:i') : 'N/A' }}</p>
                                                </div>
                                                <div class="p-4 bg-white rounded-2xl border border-slate-100 shadow-sm text-right">
                                                    <p class="text-[9px] font-black text-slate-400 uppercase mb-1 italic">ID Transaksi:</p>
                                                    <p class="text-[9px] font-black text-blue-600 uppercase">{{ $registration->payment->uid }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <div class="h-full min-h-[300px] flex flex-col items-center justify-center bg-white rounded-3xl border-2 border-emerald-100 border-dashed p-10 text-center shadow-sm">
                                            <div class="w-20 h-20 bg-emerald-50 rounded-full flex items-center justify-center text-emerald-500 shadow-inner mb-6 ring-4 ring-emerald-50/50">
                                                <i data-lucide="gift" class="w-10 h-10"></i>
                                            </div>
                                            <h5 class="text-base font-black text-emerald-800 uppercase italic">Pendaftaran Gratis</h5>
                                            <p class="text-xs text-emerald-600/70 mt-3 font-medium px-4">Nomor lomba ini tidak memerlukan biaya pendaftaran. Lu bebas ACC tanpa perlu cek bukti bayar!</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="p-8 bg-slate-50/80 border-t border-slate-100 flex gap-4 justify-end">
                            <button type="button" data-modal-hide="modal-review-registrasi-{{ $registration->uid }}" class="px-8 py-4 text-xs font-black text-slate-400 uppercase tracking-widest hover:text-slate-900 transition flex items-center gap-2">
                                <i data-lucide="corner-up-left" class="w-4 h-4"></i> Batalkan
                            </button>
                            <button type="submit" class="px-12 py-5 bg-blue-600 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest shadow-2xl shadow-blue-200 hover:bg-blue-700 transition-all hover:-translate-y-1 flex items-center gap-3">
                                <i data-lucide="save" class="w-4 h-4"></i> Simpan Status Verifikasi
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- MODAL HAPUS --}}
        <div id="modal-hapus-registrasi-{{ $registration->uid }}" tabindex="-1" aria-hidden="true"
            class="hidden fixed top-0 left-0 right-0 z-[70] w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-full max-h-full items-center justify-center bg-slate-900/60 backdrop-blur-sm transition-opacity">
            <div class="relative w-full max-w-sm max-h-full">
                <div class="relative bg-white rounded-3xl shadow-2xl border border-slate-200 p-10 text-center">
                    <div class="w-24 h-24 bg-red-50 rounded-full flex items-center justify-center mx-auto mb-8 ring-8 ring-red-50/50">
                        <i data-lucide="trash-2" class="text-red-600 w-12 h-12"></i>
                    </div>
                    <h3 class="mb-3 text-2xl font-black text-slate-900 uppercase">Hapus Data?</h3>
                    <p class="mb-10 text-sm text-slate-400 font-bold uppercase tracking-tight italic">Pendaftaran atlet ini akan dihapus permanen dari riwayat perlombaan.</p>
                    <div class="flex flex-col gap-4">
                        <form action="{{ url('/' . $user['nama_role'] . '/' . $user['uid'] . '/' . $registration->uid . '/dashboard/management-registration/delete/process') }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full py-5 bg-red-600 text-white rounded-2xl font-black text-[10px] uppercase tracking-widest shadow-2xl shadow-red-200 hover:bg-red-700 transition-all hover:-translate-y-1">Ya, Saya Yakin Hapus</button>
                        </form>
                        <button data-modal-hide="modal-hapus-registrasi-{{ $registration->uid }}" type="button" class="w-full py-5 bg-slate-100 text-slate-500 rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-slate-200 transition">Batalkan Saja</button>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

@endsection
