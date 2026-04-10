@extends('layouts.layout-homepage.app')

<style>
    /* 1. Reset Dasar & Typography Quill */
    .prose {
        max-width: none;
        color: #475569;
        line-height: 1.8;
        font-size: 1.05rem;
    }

    .prose p {
        margin-bottom: 1.25rem;
    }

    .prose strong {
        color: #0f172a;
        font-weight: 700;
    }

    .prose a {
        color: #1e40af !important;
        text-decoration: underline;
        text-underline-offset: 4px;
        font-weight: 600;
        transition: all 0.2s;
    }

    /* 2. Styling List (Penting: Quill menggunakan padding-left untuk list) */
    .prose ul,
    .prose ol {
        margin-bottom: 1.5rem;
        padding-left: 1.5rem;
        list-style-position: outside;
    }

    .prose ul {
        list-style-type: disc;
    }

    .prose ol {
        list-style-type: decimal;
    }

    .prose li {
        margin-bottom: 0.5rem;
        padding-left: 0.5rem;
    }

    /* 3. Styling Blockquote (Kutipan) */
    .prose blockquote {
        border-left: 4px solid #3b82f6;
        padding: 1rem 1.5rem;
        background-color: #f8fafc;
        font-style: italic;
        color: #1e293b;
        margin: 1.5rem 0;
        border-radius: 0 8px 8px 0;
    }

    /* 4. Styling Tabel Modern */
    .prose table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        margin: 2rem 0;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        overflow: hidden;
    }

    .prose th {
        background-color: #f8fafc;
        padding: 14px 20px;
        border-bottom: 2px solid #e2e8f0;
        text-align: left;
    }

    .prose td {
        padding: 14px 20px;
        border-bottom: 1px solid #f1f5f9;
    }

    .prose tr:last-child td {
        border-bottom: none;
    }

    /* 5. Media Responsif */
    .prose img {
        border-radius: 12px;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        margin: 2rem auto;
        display: block;
        max-width: 100%;
        height: auto;
    }
</style>

@section('homepage-section')
    {{-- HERO SECTION --}}
    <section class="relative h-[80vh] flex items-center justify-center text-center bg-cover bg-center overflow-hidden"
        style="background-image: linear-gradient(to top, rgba(15, 23, 42, 1), rgba(15, 23, 42, 0.4)), url({{ url('/file/banner-event/' . $event['banner_event']) }});">

        <div class="absolute inset-0 bg-blue-900/10 backdrop-blur-[1px]"></div>

        <div class="container mx-auto px-6 z-10">
            <div class="max-w-4xl mx-auto">
                {{-- Badge Kategori & Tipe --}}
                <div class="flex justify-center items-center gap-3 mb-6">
                    <span
                        class="px-4 py-1.5 bg-white/10 backdrop-blur-md text-white border border-white/20 text-[10px] font-black uppercase tracking-widest rounded-full">
                        {{ $event['kategori']['nama_kategori'] ?? 'General' }}
                    </span>
                    <span
                        class="px-4 py-1.5 bg-ksc-accent text-slate-900 text-[10px] font-black uppercase tracking-widest rounded-full shadow-lg shadow-ksc-accent/20">
                        {{ $event['tipe_event'] }}
                    </span>
                </div>

                <h1 class="text-4xl md:text-7xl font-black text-white leading-tight mb-8 drop-shadow-2xl">
                    {{ $event['nama_event'] }}
                </h1>

                {{-- Author & Date Info --}}
                <div class="flex flex-wrap justify-center items-center gap-6 text-white/90">
                    <div class="flex items-center gap-2 group">
                        <div
                            class="w-10 h-10 rounded-full bg-ksc-accent/20 flex items-center justify-center border border-ksc-accent/30">
                            <i data-lucide="user" class="w-5 h-5 text-ksc-accent"></i>
                        </div>
                        <div class="text-left">
                            <p class="text-[9px] uppercase font-bold text-slate-400">Organized by</p>
                            <p class="text-sm font-black">{{ $event['author'] ?? 'Admin KSC' }}</p>
                        </div>
                    </div>
                    <div class="h-8 w-px bg-white/20 hidden md:block"></div>
                    <div class="flex items-center gap-3">
                        <i data-lucide="calendar" class="w-6 h-6 text-ksc-accent"></i>
                        <span
                            class="text-lg font-bold">{{ \Carbon\Carbon::parse($event['tanggal_event'])->translatedFormat('d F Y') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- MAIN CONTENT --}}
    <section class="py-24 bg-slate-50 relative -mt-10 rounded-t-[3rem] z-20">
        <div class="container mx-auto px-6">
            <div class="grid lg:grid-cols-3 gap-12">
                {{-- Kolom Kiri - Deskripsi --}}
                <div class="lg:col-span-2">
                    <div class="bg-white p-8 md:p-12 rounded-[2.5rem] shadow-sm border border-slate-100">
                        <div class="flex items-center gap-4 mb-10">
                            <div class="h-10 w-2 bg-ksc-blue rounded-full"></div>
                            <h2 class="text-3xl font-black text-slate-900 uppercase italic">Detail Event</h2>
                        </div>
                        <div class="prose prose-slate">
                            <div class="ql-editor !p-0">
                                {!! $event['deskripsi'] !!}
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Kolom Kanan - Sidebar --}}
                <div class="lg:col-span-1">
                    <div class="sticky top-28 space-y-6">
                        <div
                            class="bg-white rounded-[2.5rem] border border-slate-100 shadow-2xl shadow-slate-200/50 overflow-hidden">
                            {{-- Price Tag --}}
                            <div class="bg-slate-900 p-8 text-white">
                                <h3 class="text-xs font-bold uppercase tracking-widest text-slate-400 mb-2">HTM / Investasi
                                </h3>
                                <div class="text-4xl font-black text-ksc-accent">
                                    {{ $event['biaya_event'] > 0 ? 'Rp ' . number_format($event['biaya_event'], 0, ',', '.') : 'GRATIS' }}
                                </div>
                            </div>

                            <div class="p-8 space-y-4">
                                {{-- Detail Info Cards --}}
                                <div class="grid grid-cols-1 gap-3">
                                    {{-- Info Lokasi --}}
                                    <div
                                        class="flex items-center gap-4 p-4 rounded-2xl bg-slate-50 border border-slate-100">
                                        <div
                                            class="w-10 h-10 rounded-xl bg-white shadow-sm flex items-center justify-center text-ksc-blue">
                                            <i data-lucide="map-pin" class="w-5 h-5"></i>
                                        </div>
                                        <div>
                                            <p class="text-[9px] text-slate-400 font-bold uppercase">Lokasi</p>
                                            <p class="text-sm font-black text-slate-800">{{ $event['lokasi_event'] }}</p>
                                        </div>
                                    </div>
                                    {{-- Info Waktu --}}
                                    <div
                                        class="flex items-center gap-4 p-4 rounded-2xl bg-slate-50 border border-slate-100">
                                        <div
                                            class="w-10 h-10 rounded-xl bg-white shadow-sm flex items-center justify-center text-ksc-blue">
                                            <i data-lucide="clock" class="w-5 h-5"></i>
                                        </div>
                                        <div>
                                            <p class="text-[9px] text-slate-400 font-bold uppercase">Waktu Mulai</p>
                                            <p class="text-sm font-black text-slate-800">
                                                {{ \Carbon\Carbon::parse($event['waktu_event'])->format('H:i') }} WIB</p>
                                        </div>
                                    </div>
                                    {{-- Info Kuota --}}
                                    <div
                                        class="flex items-center gap-4 p-4 rounded-2xl bg-blue-50/50 border border-blue-100">
                                        <div
                                            class="w-10 h-10 rounded-xl bg-white shadow-sm flex items-center justify-center text-blue-600">
                                            <i data-lucide="users" class="w-5 h-5"></i>
                                        </div>
                                        <div>
                                            <p class="text-[9px] text-blue-400 font-bold uppercase">Kuota Peserta</p>
                                            <p class="text-sm font-black text-slate-800">{{ $event['registrations_count'] }}
                                                / {{ $event['kuota_peserta'] }} Orang</p>
                                        </div>
                                    </div>
                                </div>

                                {{-- Action Button --}}
                                @if ($event['status_event'] == 'berjalan')
                                    @if ($user)
                                        @php 
                                            $totalCats = count($event['eventCategories'] ?? []);
                                            $regCount = count($registeredCategoryUids ?? []);
                                            $isAllRegistered = ($totalCats > 0 && $regCount >= $totalCats);
                                        @endphp

                                        @if ($isAllRegistered)
                                            <div class="w-full py-5 bg-emerald-100 text-emerald-700 rounded-2xl font-black flex items-center justify-center gap-3 italic text-center px-4">
                                                ANDA SUDAH TERDAFTAR DI SEMUA KATEGORI <i data-lucide="check-check" class="w-5 h-5"></i>
                                            </div>
                                            <p class="text-[9px] text-center text-slate-400 mt-2 font-bold uppercase tracking-widest">
                                                Cek Dashbord untuk status validasi
                                            </p>
                                        @elseif ($profileCompletion['complete'])
                                            <button data-modal-target="registration-modal"
                                                data-modal-toggle="registration-modal"
                                                class="w-full py-5 bg-ksc-blue hover:bg-ksc-dark text-white rounded-2xl font-black shadow-xl shadow-blue-200 transition transform hover:-translate-y-1 flex items-center justify-center gap-3">
                                                DAFTAR SEKARANG <i data-lucide="arrow-right" class="w-5 h-5"></i>
                                            </button>
                                        @else
                                            <div class="space-y-3">
                                                <a href="{{ url('/' . $user['nama_role'] . '/profile') }}"
                                                    class="w-full py-5 bg-rose-500 hover:bg-rose-600 text-white rounded-2xl font-black shadow-xl shadow-rose-100 transition transform hover:-translate-y-1 flex items-center justify-center gap-3 text-center">
                                                    LENGKAPI PROFIL ({{ $profileCompletion['percentage'] }}%) <i data-lucide="user-cog" class="w-5 h-5"></i>
                                                </a>
                                                <p class="text-[9px] text-center text-rose-500 font-bold uppercase tracking-widest italic">
                                                    *Profil harus 100% lengkap untuk mendaftar
                                                </p>
                                            </div>
                                        @endif
                                    @else
                                        <div class="space-y-3">
                                            <a href="/login"
                                                class="w-full py-5 bg-slate-900 hover:bg-black text-white rounded-2xl font-black shadow-xl shadow-slate-200 transition transform hover:-translate-y-1 flex items-center justify-center gap-3 text-center">
                                                MASUK UNTUK DAFTAR <i data-lucide="log-in" class="w-5 h-5"></i>
                                            </a>
                                            <p class="text-[9px] text-center text-slate-400 font-bold uppercase tracking-widest italic">
                                                Belum punya akun? <a href="/register" class="text-ksc-blue underline">Daftar Member</a>
                                            </p>
                                        </div>
                                    @endif
                                @else
                                    <button disabled
                                        class="w-full py-5 bg-slate-200 text-slate-400 rounded-2xl font-black cursor-not-allowed flex items-center justify-center gap-3 italic uppercase">
                                        Pendaftaran Ditutup <i data-lucide="lock" class="w-5 h-5"></i>
                                    </button>
                                @endif

                                <button onclick="openShareModal('{{ $event['nama_event'] }}', '{{ current_url() }}')"
                                    class="w-full py-3 border-2 border-slate-100 text-slate-500 rounded-2xl font-bold hover:bg-slate-50 transition flex items-center justify-center gap-2">
                                    <i data-lucide="share-2" class="w-4 h-4"></i> Bagikan Event
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- REGISTRATION MODAL --}}
    <div id="registration-modal" tabindex="-1" aria-hidden="true"
        class="fixed top-0 left-0 right-0 z-[110] hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-full bg-slate-900/60 backdrop-blur-md">
        <div class="relative w-full max-w-md flex items-center justify-center min-h-screen mx-auto">
            <div
                class="relative bg-white rounded-[3rem] shadow-2xl border border-white/20 overflow-hidden transform transition-all w-full">

                <button type="button"
                    class="absolute top-6 right-6 z-20 text-slate-400 hover:text-red-500 transition-colors"
                    data-modal-toggle="registration-modal">
                    <i data-lucide="x" class="w-6 h-6"></i>
                </button>

                <form action="{{ url('/registration/event/create/process') }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="uid_user" value="{{ $user['uid'] ?? '' }}">
                    <input type="hidden" name="uid_event" value="{{ $event['uid'] }}">

                    <div class="p-8 md:p-10 pt-12">
                        {{-- Kategori Lomba (Swimming Category) - MULTI SELECT --}}
                        <div class="mb-8">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-2 block italic mb-4">
                                Pilih Nomor Lomba (Bisa pilih lebih dari satu)
                            </label>
                            
                            <div class="grid grid-cols-1 gap-3 max-h-60 overflow-y-auto pr-2 custom-scrollbar">
                                @foreach($event['eventCategories'] as $cat)
                                    @php 
                                        $isReg = in_array($cat['uid'], $registeredCategoryUids ?? []); 
                                    @endphp
                                    <label class="relative flex items-center p-4 {{ $isReg ? 'bg-slate-100 opacity-70 cursor-not-allowed' : 'bg-slate-50 border-2 border-slate-100 cursor-pointer hover:border-ksc-blue/30' }} rounded-2xl transition-all group">
                                        <input type="checkbox" name="uid_event_category[]" value="{{ $cat['uid'] }}" 
                                            {{ $isReg ? 'disabled checked' : '' }}
                                            data-price="{{ $cat['biaya_pendaftaran'] > 0 ? $cat['biaya_pendaftaran'] : $event['biaya_event'] }}"
                                            class="w-5 h-5 text-ksc-blue border-slate-300 rounded focus:ring-ksc-blue focus:ring-2 transition-all category-checkbox">
                                        
                                        <div class="ml-4 flex-grow">
                                            <div class="flex justify-between items-start">
                                                <div>
                                                    <p class="text-[9px] font-black {{ $isReg ? 'text-slate-400' : 'text-ksc-blue' }} uppercase leading-none mb-1">
                                                        {{ $cat['category']['nama_kategori'] ?? 'Kategori' }}
                                                        @if($isReg)
                                                            <span class="ml-2 px-1.5 py-0.5 bg-ksc-blue text-white rounded text-[7px] tracking-widest">TERDAFTAR</span>
                                                        @endif
                                                    </p>
                                                    <p class="text-sm font-black text-slate-900 group-hover:text-ksc-blue transition-colors">
                                                        {{ $cat['nama_acara'] ?? $cat['nomor_lomba'] ?? 'Nomor Lomba' }}
                                                    </p>
                                                </div>
                                                <div class="text-right">
                                                    <p class="text-xs font-black text-slate-900 italic">
                                                        @if($isReg)
                                                            ---
                                                        @elseif($cat['biaya_pendaftaran'] > 0)
                                                            Rp{{ number_format($cat['biaya_pendaftaran'], 0, ',', '.') }}
                                                        @else
                                                            {{ $event['biaya_event'] > 0 ? 'Rp' . number_format($event['biaya_event'], 0, ',', '.') : 'GRATIS' }}
                                                        @endif
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="flex items-center gap-3 mt-2">
                                                <span class="text-[8px] font-bold text-slate-400 uppercase flex items-center gap-1">
                                                    <i data-lucide="clock" class="w-3 h-3"></i> {{ $cat['waktu_mulai'] ?? '-' }} WIB
                                                </span>
                                                <span class="text-[8px] font-bold text-slate-400 uppercase flex items-center gap-1">
                                                    <i data-lucide="map-pin" class="w-3 h-3"></i> {{ $cat['lokasi'] ?? '-' }}
                                                </span>
                                            </div>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        {{-- Dynamic Payment Section --}}
                        <div id="payment-section" class="hidden">
                            <div class="text-center mb-8">
                                <h3 class="text-2xl font-black text-slate-900 italic uppercase leading-tight">Satu Langkah Lagi</h3>
                                <p class="text-xs text-slate-400 mt-2 font-bold uppercase tracking-widest">Selesaikan Pembayaran Anda</p>
                            </div>

                            @if($event['rekening'])
                                {{-- Pembayaran Box (Info Rekening) --}}
                                <div class="bg-ksc-blue rounded-[2rem] p-6 mb-6 text-white relative overflow-hidden shadow-xl shadow-blue-100">
                                    <div class="absolute -right-4 -bottom-4 w-20 h-20 bg-white/5 rounded-full blur-2xl"></div>
                                    <div class="flex items-center justify-between mb-4 pb-4 border-b border-white/10">
                                        <span class="text-[10px] font-bold text-blue-200 uppercase tracking-widest">Total Harus Dibayar</span>
                                        <div class="px-3 py-1 bg-white/10 rounded-lg text-[10px] font-bold tracking-widest">UNPAID</div>
                                    </div>
                                    <div class="flex items-end justify-between gap-4">
                                        <div>
                                            <p class="text-[9px] font-black text-blue-300 uppercase tracking-widest leading-none mb-1">Total Biaya</p>
                                            <h2 id="display-total-price" class="text-xl font-black text-ksc-accent italic tracking-tighter">Rp 0</h2>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-[9px] font-black text-blue-300 uppercase tracking-widest leading-none mb-1">{{ $event['bank'] ?? 'Bank Transfer' }}</p>
                                            <p class="text-xs font-black tracking-widest">{{ $event['rekening'] }}</p>
                                            <p class="text-[8px] font-bold italic opacity-70">A/N {{ $event['atas_nama'] }}</p>
                                        </div>
                                    </div>
                                </div>

                                @if ($event['photo'] != null)
                                    <div class="relative w-full p-4 bg-slate-50 rounded-[2rem] border-2 border-dashed border-slate-200 mb-6 text-center">
                                        <img src="{{ url('/file/kode-bank/' . $event['photo']) }}" alt="QRIS"
                                            class="w-40 h-40 object-contain mx-auto mix-blend-multiply">
                                        <p class="mt-2 text-[10px] font-black text-ksc-blue uppercase tracking-widest italic">
                                            Opsi: Scan QRIS via E-Wallet
                                        </p>
                                    </div>
                                @endif
                            @else
                                <div class="bg-amber-50 border-2 border-amber-100 rounded-2xl p-4 mb-6 text-amber-900 flex items-center gap-3">
                                    <i data-lucide="alert-circle" class="w-5 h-5 flex-shrink-0"></i>
                                    <p class="text-xs font-bold leading-tight">Metode pembayaran belum diatur. Harap hubungi panitia untuk informasi transfer.</p>
                                </div>
                            @endif

                            {{-- INPUT METODE & TOTAL BAYAR --}}
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                                <div>
                                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-2 block italic mb-2">
                                        Metode Pembayaran
                                    </label>
                                    <select name="metode_pembayaran" id="metode_pembayaran"
                                        class="bg-white border-2 border-slate-100 text-slate-900 text-xs font-bold rounded-2xl block w-full p-3.5 outline-none focus:border-ksc-blue transition-all cursor-pointer">
                                        <option value="" disabled selected>Pilih Metode...</option>
                                        <optgroup label="Transfer Bank">
                                            <option value="{{ $event['bank'] ?? 'Transfer Bank' }}">Transfer Ke Rekening Panitia</option>
                                            <option value="Tunai">Tunai / Cash</option>
                                        </optgroup>
                                        <optgroup label="E-Wallet">
                                            <option value="GoPay">GoPay</option>
                                            <option value="OVO">OVO</option>
                                            <option value="Dana">Dana</option>
                                        </optgroup>
                                    </select>
                                </div>

                                <div>
                                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-2 block italic mb-2">
                                        Total Bayar (IDR)
                                    </label>
                                    <input type="number" name="total_bayar" id="input-total-price" readonly
                                        class="bg-slate-50 border-2 border-slate-100 text-slate-400 text-xs font-bold rounded-2xl block w-full p-3.5 outline-none cursor-not-allowed">
                                </div>
                            </div>

                            {{-- UPLOAD BUKTI --}}
                            <div class="space-y-3 mb-6">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-2 block italic">
                                    Unggah Bukti Transfer
                                </label>
                                <div class="relative group">
                                    <input type="file" name="bukti_pembayaran" id="bukti_input"
                                        class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
                                        onchange="previewBukti(this)">

                                    <div id="upload-placeholder"
                                        class="flex flex-col items-center justify-center p-6 bg-slate-50 border-2 border-dashed border-slate-200 rounded-[2rem] group-hover:border-ksc-blue group-hover:bg-blue-50 transition-all">
                                        <div class="w-12 h-12 bg-white shadow-sm rounded-xl flex items-center justify-center text-slate-400 mb-2 group-hover:text-ksc-blue transition-colors">
                                            <i data-lucide="camera" class="w-6 h-6"></i>
                                        </div>
                                        <p id="file-label" class="text-xs font-black text-slate-700 uppercase">Pilih Foto Bukti</p>
                                    </div>

                                    <div id="preview-container" class="hidden relative rounded-[2rem] overflow-hidden border-2 border-ksc-blue shadow-lg">
                                        <img id="image-preview" src="#" class="w-full h-48 object-cover">
                                        <div class="absolute inset-0 bg-slate-900/40 flex items-center justify-center opacity-0 hover:opacity-100 transition-opacity">
                                            <p class="text-white text-[10px] font-bold uppercase tracking-widest">Ganti Foto</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Free Access Info --}}
                        <div id="free-access-info" class="text-center py-6">
                            <div class="w-16 h-16 bg-blue-50 text-ksc-blue rounded-2xl flex items-center justify-center mx-auto mb-4">
                                <i data-lucide="gift" class="w-8 h-8"></i>
                            </div>
                            <h3 class="text-xl font-black text-slate-900 italic uppercase">Pendaftaran Gratis</h3>
                            <p class="text-[10px] text-slate-500 mt-1 px-6 uppercase font-bold tracking-widest">Klik konfirmasi untuk mengamankan slot Anda.</p>
                        </div>

                        <div class="mt-10">
                            <button type="submit"
                                class="w-full py-5 bg-ksc-blue hover:bg-ksc-dark text-white rounded-[1.5rem] font-black shadow-2xl shadow-blue-200 transition active:scale-95 flex items-center justify-center gap-3 group">
                                <span>{{ $event['biaya_event'] > 0 ? 'KIRIM KONFIRMASI' : 'DAFTAR SEKARANG' }}</span>
                                <i data-lucide="check-circle"
                                    class="w-5 h-5 group-hover:scale-110 transition-transform"></i>
                            </button>
                            <p
                                class="text-[9px] text-center text-slate-400 mt-4 font-bold uppercase tracking-widest italic">
                                *Admin akan memverifikasi dalam 1x24 jam.
                            </p>
                        </div>
                    </div>
                </form>

                <script>
                    // Fungsi Preview Gambar
                    function previewBukti(input) {
                        const placeholder = document.getElementById('upload-placeholder');
                        const container = document.getElementById('preview-container');
                        const preview = document.getElementById('image-preview');

                        if (input.files && input.files[0]) {
                            const reader = new FileReader();
                            reader.onload = function(e) {
                                preview.src = e.target.result;
                                placeholder.classList.add('hidden');
                                container.classList.remove('hidden');
                            }
                            reader.readAsDataURL(input.files[0]);
                        }
                    }
                </script>
            </div>
        </div>
    </div>

    {{-- SHARE MODAL --}}
    <div id="shareModal" class="fixed inset-0 z-[120] hidden items-center justify-center px-4">
        <div class="absolute inset-0 bg-slate-900/70 backdrop-blur-md" onclick="closeShareModal()"></div>
        <div class="relative bg-white w-full max-w-sm rounded-[2.5rem] shadow-2xl p-10 transform transition-all duration-300 scale-95 opacity-0"
            id="shareContent">
            <div class="text-center mb-10">
                <h3 class="text-2xl font-black text-slate-900 italic uppercase tracking-tight">Bagikan</h3>
                <p class="text-xs text-slate-400 mt-1 font-bold uppercase tracking-widest">Sebarkan kabar gembira ini</p>
            </div>
            <div class="grid grid-cols-3 gap-8 mb-10 text-center">
                <a id="shareWA" target="_blank" class="flex flex-col items-center gap-2 group">
                    <div
                        class="w-14 h-14 bg-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center group-hover:bg-emerald-600 group-hover:text-white transition-all shadow-sm">
                        <i data-lucide="message-circle" class="w-6 h-6"></i>
                    </div>
                    <span class="text-[8px] font-black text-slate-500 uppercase">WhatsApp</span>
                </a>
                <a id="shareFB" target="_blank" class="flex flex-col items-center gap-2 group">
                    <div
                        class="w-14 h-14 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center group-hover:bg-blue-600 group-hover:text-white transition-all shadow-sm">
                        <i data-lucide="facebook" class="w-6 h-6"></i>
                    </div>
                    <span class="text-[8px] font-black text-slate-500 uppercase">Facebook</span>
                </a>
                <button onclick="copyToClipboard()" class="flex flex-col items-center gap-2 group text-center">
                    <div
                        class="w-14 h-14 bg-slate-50 text-slate-600 rounded-2xl flex items-center justify-center group-hover:bg-ksc-blue group-hover:text-white transition-all shadow-sm">
                        <i data-lucide="copy" class="w-6 h-6"></i>
                    </div>
                    <span id="copyTextLabel" class="text-[8px] font-black text-slate-500 uppercase">Copy Link</span>
                </button>
            </div>
            <button onclick="closeShareModal()"
                class="w-full py-4 bg-slate-100 text-slate-500 font-black rounded-2xl hover:bg-slate-200 transition uppercase text-[10px] tracking-widest">Batal</button>
        </div>
    </div>

    <script>
        // Fitur Preview Gambar
        function previewBukti(input) {
            const container = document.getElementById('preview-container');
            const placeholder = document.getElementById('upload-placeholder');
            const image = document.getElementById('image-preview');

            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    image.src = e.target.result;
                    placeholder.classList.add('hidden');
                    container.classList.remove('hidden');
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        // Logic Share & Modal
        let currentShareUrl = "";

        function openShareModal(title, url) {
            currentShareUrl = url;
            const modal = document.getElementById('shareModal');
            const content = document.getElementById('shareContent');
            document.getElementById('shareWA').href = `https://wa.me/?text=${encodeURIComponent(title + ' - ' + url)}`;
            document.getElementById('shareFB').href =
                `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(url)}`;
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            setTimeout(() => content.classList.replace('opacity-0', 'opacity-100'), 10);
        }

        function closeShareModal() {
            const modal = document.getElementById('shareModal');
            modal.classList.replace('flex', 'hidden');
        }
        async function copyToClipboard() {
            await navigator.clipboard.writeText(currentShareUrl);
            document.getElementById('copyTextLabel').innerText = "Copied!";
            setTimeout(() => document.getElementById('copyTextLabel').innerText = "Copy Link", 2000);
        }

        document.addEventListener('DOMContentLoaded', () => {
            lucide.createIcons();

            // Dynamic Price Calculation
            const checkboxes = document.querySelectorAll('.category-checkbox');
            function updateTotal() {
                let total = 0;
                let selectedCount = 0;
                checkboxes.forEach(cb => {
                    if (cb.checked && !cb.disabled) {
                        total += parseFloat(cb.getAttribute('data-price') || 0);
                        selectedCount++;
                    }
                });

                const displayTotal = document.getElementById('display-total-price');
                const inputTotal = document.getElementById('input-total-price');
                const paymentSection = document.getElementById('payment-section');
                const freeAccessInfo = document.getElementById('free-access-info');
                const buktiInput = document.getElementById('bukti_input');
                const metodeSelect = document.getElementById('metode_pembayaran');

                if (displayTotal) {
                    displayTotal.innerText = `Rp ${new Intl.NumberFormat('id-ID').format(total)}`;
                }
                if (inputTotal) {
                    inputTotal.value = total;
                }

                if (total > 0) {
                    paymentSection.classList.remove('hidden');
                    freeAccessInfo.classList.add('hidden');
                    if (buktiInput) buktiInput.setAttribute('required', 'required');
                    if (metodeSelect) metodeSelect.setAttribute('required', 'required');
                } else {
                    paymentSection.classList.add('hidden');
                    freeAccessInfo.classList.remove('hidden');
                    if (buktiInput) buktiInput.removeAttribute('required');
                    if (metodeSelect) metodeSelect.removeAttribute('required');
                }
            }

            checkboxes.forEach(cb => {
                cb.addEventListener('change', updateTotal);
            });

            // Initial call
            updateTotal();
        });
    </script>
@endsection
