@extends('layouts.layout-homepage.app')

<style>
    /* Custom Scrollbar */
    ::-webkit-scrollbar {
        width: 8px;
    }

    ::-webkit-scrollbar-track {
        background: #f1f1f1;
    }

    ::-webkit-scrollbar-thumb {
        background: #94a3b8;
        border-radius: 4px;
    }

    ::-webkit-scrollbar-thumb:hover {
        background: #64748b;
    }

    /* Glass Effect & Hover */
    .glass-card {
        background: rgba(255, 255, 255, 0.03);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.1);
    }

    .event-card-hover {
        transition: all 0.3s ease;
    }

    .event-card-hover:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 25px -5px rgb(0 0 0 / 0.1);
    }

    /* Animation */
    .reveal {
        opacity: 0;
        transform: translateY(30px);
        transition: all 0.8s ease-out;
    }

    .reveal.active {
        opacity: 1;
        transform: translateY(0);
    }

    .scrollbar-hide::-webkit-scrollbar {
        display: none;
    }

    .scrollbar-hide {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }
</style>

@section('homepage-section')
    <section class="relative pt-32 pb-20 bg-hero-lomba bg-cover bg-center bg-slate-900">
        <div class="container mx-auto px-6 relative z-10 text-center text-white">
            <div
                class="inline-block px-4 py-1.5 bg-ksc-accent/20 backdrop-blur-md border border-ksc-accent/30 rounded-full text-ksc-accent font-bold text-xs mb-6 uppercase tracking-widest">
                Arena Kompetisi
            </div>
            <h1 class="text-4xl md:text-6xl font-bold mb-6 leading-tight">
                Tunjukkan Kemampuanmu <br>
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-ksc-accent to-yellow-200">Raih Podium
                    Juara.</span>
            </h1>
            <p class="text-slate-300 text-lg max-w-2xl mx-auto mb-10">
                Jadwal lengkap kompetisi renang internal dan terbuka. Daftarkan dirimu dan jadilah bagian dari sejarah
                prestasi KSC.
            </p>
        </div>
    </section>

    <section class="py-8 bg-white sticky top-[72px] z-40 border-b border-slate-100">
        <div class="container mx-auto px-6">
            <div class="flex flex-col gap-8" id="event-filter-bar">
                <div class="relative w-full max-w-5xl mx-auto group">
                    <div class="absolute left-7 top-1/2 -translate-y-1/2 z-10">
                        <i data-lucide="search" class="w-6 h-6 text-ksc-blue"></i>
                    </div>

                    <input type="text" id="eventSearchInput" placeholder="CARI NAMA LOMBA ATAU LOKASI KOMPETISI..."
                        class="w-full bg-slate-50 border border-slate-200 rounded-[2rem] py-5 pl-16 pr-8 text-sm font-bold tracking-wider text-slate-800 outline-none transition-all focus:bg-white focus:ring-[12px] focus:ring-ksc-blue/5 focus:border-ksc-blue/40 placeholder:text-slate-400 uppercase">
                </div>

            </div>
        </div>
    </section>

    <section class="py-20 overflow-hidden bg-slate-50/50">
        <div class="container mx-auto px-6">
            <div class="grid lg:grid-cols-3 md:grid-cols-2 gap-8">
                @foreach ($events['data'] as $event)
                    @if ($event['status_event'] === 'berjalan')
                        @if ($event['tipe_event'] === 'berbayar')
                            <div class="event-card group bg-white rounded-[2.5rem] border border-slate-100 shadow-sm hover:shadow-2xl hover:shadow-slate-200 transition-all duration-300 overflow-hidden flex flex-col"
                                data-status="berjalan">
                                <div class="relative h-48 overflow-hidden">
                                    @if ($event->banner_event === null)
                                        <img src="https://lh5.googleusercontent.com/proxy/t08n2HuxPfw8OpbutGWjekHAgxfPFv-pZZ5_-uTfhEGK8B5Lp-VN4VjrdxKtr8acgJA93S14m9NdELzjafFfy13b68pQ7zzDiAmn4Xg8LvsTw1jogn_7wStYeOx7ojx5h63Gliw"
                                            alt="Banner Event" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                                    @else
                                        <img src="{{ url('/file/banner-event/' . $event['banner_event']) }}"
                                            alt="Banner Event" class="w-16 h-10 object-cover rounded">
                                    @endif
                                    <div class="absolute top-4 left-4 flex gap-2">
                                        <span
                                            class="px-3 py-1 bg-ksc-accent text-slate-900 text-[10px] font-black uppercase rounded-full shadow-lg">Berbayar</span>
                                        <span
                                            class="px-3 py-1 bg-emerald-500 text-white text-[10px] font-black uppercase rounded-full shadow-lg">Berjalan</span>
                                    </div>
                                </div>

                                <div class="p-6 flex-grow">
                                    <div class="flex justify-between items-start mb-2">
                                        <p class="text-[10px] font-bold text-ksc-blue uppercase tracking-widest">
                                            {{ $event['category']['nama_kategori'] }}</p>
                                        <div class="flex items-center gap-1 text-slate-500">
                                            <i data-lucide="clock" class="w-3 h-3 text-ksc-blue"></i>
                                            <span
                                                class="text-[10px] font-black">{{ \Carbon\Carbon::parse($event['waktu_event'])->translatedFormat('H:m') }}
                                                WIB</span>
                                        </div>
                                    </div>
                                    <h3
                                        class="text-xl font-black text-slate-900 leading-tight mb-4 group-hover:text-ksc-blue transition-colors line-clamp-2">
                                        {{ $event['nama_event'] }}</h3>

                                    <div class="mb-6 bg-slate-50 p-3 rounded-2xl border border-slate-100">
                                        <div class="flex justify-between text-[10px] font-black uppercase mb-1.5">
                                            <span class="text-slate-400">Kuota Peserta</span>
                                            <span
                                                class="text-ksc-blue">{{ $event['registrations_count'] }}/{{ $event['kuota_peserta'] }}
                                                Tersedia</span>
                                        </div>
                                        <div class="w-full bg-slate-200 h-1.5 rounded-full overflow-hidden">
                                            <div class="bg-ksc-blue h-full rounded-full"
                                                style="width: {{ ($event['registrations_count'] / $event['kuota_peserta']) * 100 }}%">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="flex items-center justify-between pt-4 border-t border-slate-50">
                                        <div>
                                            <p class="text-[9px] font-bold text-slate-400 uppercase italic">Investasi</p>
                                            <p class="text-lg font-black text-slate-900">{{ rupiah($event['biaya_event']) }}
                                            </p>
                                        </div>
                                        <a href="{{ url('/detail-event/' . $event['slug'] . '/' . $event['uid']) }}"
                                            class="w-12 h-12 bg-slate-900 text-white rounded-2xl flex items-center justify-center group-hover:bg-ksc-blue transition-all active:scale-90">
                                            <i data-lucide="arrow-right" class="w-6 h-6"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="event-card group bg-white rounded-[2.5rem] border border-slate-100 shadow-sm hover:shadow-2xl hover:shadow-slate-200 transition-all duration-300 overflow-hidden flex flex-col"
                                data-status="berjalan">
                                <div class="relative h-48 overflow-hidden">
                                    <img src="{{ url('/file/banner-event/' . $event['banner_event']) }}"
                                        class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                                    <div class="absolute top-4 left-4 flex gap-2">
                                        <span
                                            class="px-3 py-1 bg-ksc-accent text-slate-900 text-[10px] font-black uppercase rounded-full shadow-lg">Gratis</span>
                                        <span
                                            class="px-3 py-1 bg-emerald-500 text-white text-[10px] font-black uppercase rounded-full shadow-lg">Berjalan</span>
                                    </div>
                                </div>

                                <div class="p-6 flex-grow">
                                    <div class="flex justify-between items-start mb-2">
                                        <p class="text-[10px] font-bold text-ksc-blue uppercase tracking-widest">
                                            {{ $event['category']['nama_kategori'] }}</p>
                                        <div class="flex items-center gap-1 text-slate-500">
                                            <i data-lucide="clock" class="w-3 h-3 text-ksc-blue"></i>
                                            <span
                                                class="text-[10px] font-black">{{ \Carbon\Carbon::parse($event['waktu_event'])->translatedFormat('H:m') }}
                                                WIB</span>
                                        </div>
                                    </div>
                                    <h3
                                        class="text-xl font-black text-slate-900 leading-tight mb-4 group-hover:text-ksc-blue transition-colors line-clamp-2">
                                        {{ $event['nama_event'] }}</h3>

                                    <div class="mb-6 bg-slate-50 p-3 rounded-2xl border border-slate-100">
                                        <div class="flex justify-between text-[10px] font-black uppercase mb-1.5">
                                            <span class="text-slate-400">Kuota Peserta</span>
                                            <span
                                                class="text-ksc-blue">{{ $event['registrations_count'] }}/{{ $event['kuota_peserta'] }}
                                                Tersedia</span>
                                        </div>
                                        <div class="w-full bg-slate-200 h-1.5 rounded-full overflow-hidden">
                                            <div class="bg-ksc-blue h-full rounded-full"
                                                style="width: {{ ($event['registrations_count'] / $event['kuota_peserta']) * 100 }}%">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="flex items-center justify-between pt-4 border-t border-slate-50">
                                        <div>
                                            <p class="text-[9px] font-bold text-slate-400 uppercase italic">Investasi</p>
                                            <p class="text-lg font-black text-green-500">GRATIS</p>
                                        </div>
                                        <a href="{{ url('/detail-event/' . $event['slug'] . '/' . $event['uid']) }}"
                                            class="w-12 h-12 bg-slate-900 text-white rounded-2xl flex items-center justify-center group-hover:bg-ksc-blue transition-all active:scale-90">
                                            <i data-lucide="arrow-right" class="w-6 h-6"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endif
                    @if ($event['status_event'] === 'ditunda')
                        <div class="event-card group bg-white rounded-[2.5rem] border border-slate-100 shadow-sm overflow-hidden flex flex-col"
                            data-status="ditunda">
                            <div class="relative h-48 overflow-hidden">
                                <img src="{{ url('/file/banner-event/' . $event['banner_event']) }}"
                                    class="w-full h-full object-cover grayscale opacity-50">
                                <div class="absolute top-4 left-4 flex gap-2">
                                    <span
                                        class="px-3 py-1 bg-white text-slate-900 text-[10px] font-black uppercase rounded-full shadow-sm">{{ $event['tipe_event'] }}</span>
                                    <span
                                        class="px-3 py-1 bg-orange-500 text-white text-[10px] font-black uppercase rounded-full shadow-lg">Ditunda</span>
                                </div>
                            </div>

                            <div class="p-6 flex-grow bg-slate-50/30">
                                <div class="flex justify-between items-start mb-2">
                                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                                        {{ $event['category']['nama_kategori'] }}</p>
                                    <div class="flex items-center gap-1 text-slate-400">
                                        <i data-lucide="clock" class="w-3 h-3"></i>
                                        <span
                                            class="text-[10px] font-black">{{ \Carbon\Carbon::parse($event['waktu_event'])->translatedFormat('H:m') }}
                                            WIB</span>
                                    </div>
                                </div>
                                <h3 class="text-xl font-black text-slate-400 leading-tight mb-4 line-clamp-2">
                                    {{ $event['nama_event'] }}
                                </h3>

                                <div class="mb-6 opacity-40">
                                    <div
                                        class="flex justify-between text-[10px] font-black uppercase mb-1.5 text-slate-400">
                                        <span>Kuota Peserta</span>
                                        <span>{{ $event['registrations_count'] }}/{{ $event['kuota_peserta'] }}
                                            Tersedia</span>
                                    </div>
                                    <div class="mb-6 bg-slate-50 p-3 rounded-2xl border border-slate-100">
                                        <div
                                            class="flex justify-between text-[10px] font-black uppercase mb-1.5 text-slate-600">
                                            <span>Status Kuota</span>
                                            <span>Ditunda</span>
                                        </div>
                                        <div class="w-full bg-slate-200 h-1.5 rounded-full overflow-hidden">
                                            <div class="bg-slate-500 h-full rounded-full" style="width: 100%"></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex items-center justify-between pt-4 border-t border-slate-200">
                                    <span class="text-[10px] italic font-black text-orange-600 uppercase">Menunggu
                                        Jadwal</span>
                                    <button disabled
                                        class="w-12 h-12 bg-slate-100 text-slate-300 rounded-2xl flex items-center justify-center cursor-not-allowed">
                                        <i data-lucide="lock" class="w-6 h-6"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endif
                    @if ($event['status_event'] === 'ditutup')
                        <div class="event-card group bg-white rounded-[2.5rem] border border-slate-100 shadow-sm overflow-hidden flex flex-col"
                            data-status="ditutup">
                            <div class="relative h-48 overflow-hidden grayscale opacity-75">
                                <img src="{{ url('/file/banner-event/' . $event['banner_event']) }}"
                                    class="w-full h-full object-cover">
                                <div class="absolute top-4 left-4 flex gap-2">
                                    <span
                                        class="px-3 py-1 bg-white text-slate-900 text-[10px] font-black uppercase rounded-full">{{ $event['tipe_event'] }}</span>
                                    <span
                                        class="px-3 py-1 bg-slate-800 text-white text-[10px] font-black uppercase rounded-full">Ditutup</span>
                                </div>
                            </div>

                            <div class="p-6 flex-grow">
                                <div class="flex justify-between items-start mb-2">
                                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                                        {{ $event['category']['nama_kategori'] }}</p>
                                    <div class="flex items-center gap-1 text-slate-400">
                                        <i data-lucide="clock" class="w-3 h-3"></i>
                                        <span class="text-[10px] font-black">Selesai</span>
                                    </div>
                                </div>
                                <h3 class="text-xl font-black text-slate-900 leading-tight mb-4 line-clamp-2">
                                    {{ $event['nama_event'] }}</h3>

                                <div class="mb-6 bg-red-50 p-3 rounded-2xl border border-red-100">
                                    <div class="flex justify-between text-[10px] font-black uppercase mb-1.5 text-red-600">
                                        <span>Status Kuota</span>
                                        <span>Penuh</span>
                                    </div>
                                    <div class="w-full bg-red-200 h-1.5 rounded-full overflow-hidden">
                                        <div class="bg-red-500 h-full rounded-full" style="width: 100%"></div>
                                    </div>
                                </div>

                                <div class="flex items-center justify-between pt-4 border-t border-slate-50">
                                    <span class="text-[10px] font-black text-red-500 italic uppercase">Pendaftaran
                                        Berakhir</span>
                                    <div
                                        class="w-12 h-12 bg-slate-50 text-slate-300 rounded-2xl flex items-center justify-center">
                                        <i data-lucide="ban" class="w-6 h-6"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
            @php
                // Cek apakah ada keyword dari controller (sesuaikan variabelnya)
                $keyword = $currentKeyword ?? null;

                // Tentukan base URL: jika ada keyword pakai format search, jika tidak pakai standar
                $paginationPath = $keyword ? '/events/search/' . urlencode($keyword) . '/page/' : '/events/page/';
            @endphp

            <div class="mt-6 flex flex-col md:flex-row items-center justify-between gap-4 px-2 pb-10">
                <div class="text-left">
                    <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest">
                        Halaman <span class="text-slate-900">{{ $events['current_page'] }}</span>
                        dari <span class="text-slate-900">{{ $events['last_page'] }}</span>
                        — Total <span class="text-ksc-blue">{{ $events['total'] }}</span> Event
                        @if ($keyword)
                            <span class="text-rose-500 italic"> (Hasil Pencarian: "{{ $keyword }}")</span>
                        @endif
                    </p>
                </div>

                <div class="flex items-center gap-2">
                    {{-- Tombol Prev --}}
                    @if ($events['current_page'] > 1)
                        <a href="{{ url($paginationPath . ($events['current_page'] - 1)) }}"
                            class="flex items-center gap-2 px-4 py-2.5 bg-white border border-slate-200 text-slate-600 rounded-xl font-bold text-xs hover:bg-slate-50 hover:border-slate-300 transition-all shadow-sm active:scale-95">
                            <i data-lucide="chevron-left" class="w-4 h-4 text-slate-400"></i>
                            <span>Prev</span>
                        </a>
                    @else
                        <div
                            class="flex items-center gap-2 px-4 py-2.5 bg-slate-50 border border-slate-100 text-slate-300 rounded-xl font-bold text-xs cursor-not-allowed opacity-60">
                            <i data-lucide="chevron-left" class="w-4 h-4"></i>
                            <span>Prev</span>
                        </div>
                    @endif

                    {{-- Indikator Halaman --}}
                    <div class="flex items-center bg-white border border-slate-200 rounded-xl px-4 py-2.5 shadow-sm">
                        <span class="text-xs font-black text-slate-900">{{ $events['current_page'] }}</span>
                    </div>

                    {{-- Tombol Next --}}
                    @if ($events['current_page'] < $events['last_page'])
                        <a href="{{ url($paginationPath . ($events['current_page'] + 1)) }}"
                            class="flex items-center gap-2 px-4 py-2.5 bg-white border border-slate-200 text-slate-600 rounded-xl font-bold text-xs hover:bg-slate-50 hover:border-slate-300 transition-all shadow-sm active:scale-95">
                            <span>Next</span>
                            <i data-lucide="chevron-right" class="w-4 h-4 text-slate-400"></i>
                        </a>
                    @else
                        <div
                            class="flex items-center gap-2 px-4 py-2.5 bg-slate-50 border border-slate-100 text-slate-300 rounded-xl font-bold text-xs cursor-not-allowed opacity-60">
                            <span>Next</span>
                            <i data-lucide="chevron-right" class="w-4 h-4"></i>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>

    <section class="py-24 bg-slate-900 relative overflow-hidden">
        <div class="container mx-auto px-6 relative z-10">
            <div class="text-center max-w-2xl mx-auto mb-20">
                <span class="text-ksc-accent font-bold tracking-widest text-xs uppercase">Workflow</span>
                <h2 class="text-4xl font-bold text-white mt-2 leading-tight">Bagaimana Cara <br> Mendaftar Kompetisi?</h2>
            </div>

            <div class="grid md:grid-cols-4 gap-12 text-left">
                <div class="relative group">
                    <div
                        class="w-16 h-16 bg-white/10 rounded-2xl flex items-center justify-center text-ksc-accent mb-8 border border-white/5 transition group-hover:bg-ksc-accent group-hover:text-slate-900 shadow-xl">
                        <i data-lucide="search" class="w-8 h-8"></i>
                    </div>
                    <h5 class="text-white font-bold mb-3 text-lg">1. Pilih Event</h5>
                    <p class="text-slate-400 text-sm leading-relaxed">Cari kompetisi yang sesuai dengan kategori umur dan
                        spesialisasi teknik renangmu.</p>
                </div>
                <div class="relative group">
                    <div
                        class="w-16 h-16 bg-white/10 rounded-2xl flex items-center justify-center text-ksc-accent mb-8 border border-white/5 transition group-hover:bg-ksc-accent group-hover:text-slate-900 shadow-xl">
                        <i data-lucide="file-text" class="w-8 h-8"></i>
                    </div>
                    <h5 class="text-white font-bold mb-3 text-lg">2. Isi Formulir</h5>
                    <p class="text-slate-400 text-sm leading-relaxed">Lengkapi profil peserta, pilih nomor lomba, dan
                        unggah
                        dokumen persyaratan wajib.</p>
                </div>
                <div class="relative group">
                    <div
                        class="w-16 h-16 bg-white/10 rounded-2xl flex items-center justify-center text-ksc-accent mb-8 border border-white/5 transition group-hover:bg-ksc-accent group-hover:text-slate-900 shadow-xl">
                        <i data-lucide="credit-card" class="w-8 h-8"></i>
                    </div>
                    <h5 class="text-white font-bold mb-3 text-lg">3. Pembayaran</h5>
                    <p class="text-slate-400 text-sm leading-relaxed">Lakukan pembayaran biaya registrasi via transfer bank
                        sesuai nominal yang tertera.</p>
                </div>
                <div class="relative group">
                    <div
                        class="w-16 h-16 bg-ksc-accent rounded-2xl flex items-center justify-center text-slate-900 mb-8 shadow-2xl transition group-hover:scale-110">
                        <i data-lucide="check-circle" class="w-8 h-8"></i>
                    </div>
                    <h5 class="text-white font-bold mb-3 text-lg">4. Konfirmasi</h5>
                    <p class="text-slate-400 text-sm leading-relaxed">Admin akan memvalidasi data Anda. E-Card resmi akan
                        dikirim melalui dashboard Anda.</p>
                </div>
            </div>
        </div>
    </section>

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            // 1. REVEAL ANIMATION (Muncul saat scroll)
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add("active");
                    }
                });
            }, {
                threshold: 0.1
            });

            document.querySelectorAll(".reveal").forEach(el => observer.observe(el));

            // 2. KONFIGURASI KELAS CSS (Tailwind)
            const activeClass =
                "filter-btn snap-start px-6 py-2 bg-ksc-blue text-white rounded-full text-sm font-bold shadow-lg shadow-ksc-blue/20 whitespace-nowrap transition-all duration-300";
            const inactiveClass =
                "filter-btn snap-start px-6 py-2 bg-white text-slate-600 border border-slate-200 hover:bg-slate-50 rounded-full text-sm font-medium whitespace-nowrap transition-all duration-300";

            // 3. INISIALISASI SWIPER
            const eventSwiper = new Swiper('.swiper-event', {
                slidesPerView: 1,
                spaceBetween: 40,
                autoHeight: true,
                speed: 700,
                allowTouchMove: true,
                on: {
                    slideChange: function() {
                        const buttons = document.querySelectorAll('#event-filter-bar .filter-btn');
                        buttons.forEach((btn, idx) => {
                            if (idx === this.activeIndex) {
                                btn.className = activeClass;
                                btn.scrollIntoView({
                                    behavior: 'smooth',
                                    inline: 'center',
                                    block: 'nearest'
                                });
                            } else {
                                btn.className = inactiveClass;
                            }
                        });
                    }
                }
            });

            // 4. NAVIGASI TOMBOL KE SLIDE
            window.goToEventSlide = function(index) {
                eventSwiper.slideTo(index);
            };

            // 5. LIVE SEARCH
            const searchInput = document.getElementById('eventSearchInput');

            if (searchInput) {
                searchInput.addEventListener('keypress', function(e) {
                    if (e.key === 'Enter') {
                        const term = e.target.value.trim();
                        if (term !== "") {
                            // Redirect ke URL cantik: /events/search/ternate
                            window.location.href = `/events/search/${encodeURIComponent(term)}`;
                        } else {
                            window.location.href = `/events`;
                        }
                    }
                });
            }

            // Inisialisasi Lucide Icons
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        });
    </script>
@endsection
