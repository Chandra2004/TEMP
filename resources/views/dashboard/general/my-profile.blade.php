@extends('layouts.layout-dashboard.app')
{{-- {{ dd($user) }} --}}

@section('dashboard-section')
<div class="p-4 md:p-8 overflow-y-auto h-screen bg-slate-50/50">
    {{-- HEADER --}}
    <div class="mb-8 flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div class="text-left">
            <h2 class="text-3xl font-black text-slate-900 leading-tight tracking-tight uppercase">Pengaturan Profil</h2>
            <p class="text-sm text-slate-500 font-medium italic">Kelola identitas digital, data fisik, dan dokumen verifikasi dalam satu panel terpadu.</p>
        </div>

        @php
            $profileStatus = $user->getProfileCompletion();
            $completionPercentage = $profileStatus['percentage'];
            $missingFields = $profileStatus['missing'];
        @endphp

        @if($completionPercentage < 100)
            <div class="flex items-center gap-3 bg-amber-50 border border-amber-200 p-3 rounded-xl">
                <div class="h-10 w-10 bg-amber-100 rounded-lg flex items-center justify-center text-amber-600 flex-shrink-0">
                    <i data-lucide="alert-triangle" class="w-6 h-6"></i>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-bold text-amber-800 uppercase tracking-wider">Profil Belum Lengkap ({{ $completionPercentage }}%)</span>
                    </div>
                    <p class="text-[10px] text-amber-600 font-medium">Data yang kurang: <span class="font-bold underline">{{ implode(', ', $missingFields) }}</span></p>
                </div>
            </div>
        @else
            <div class="flex items-center gap-3 bg-emerald-50 border border-emerald-200 p-3 rounded-xl">
                <div class="h-10 w-10 bg-emerald-100 rounded-lg flex items-center justify-center text-emerald-600 flex-shrink-0">
                    <i data-lucide="check-circle" class="w-6 h-6"></i>
                </div>
                <div>
                    <span class="text-xs font-bold text-emerald-800 uppercase tracking-wider">Profil Terverifikasi 100%</span>
                    <p class="text-[10px] text-emerald-600 font-medium">Bagus! Seluruh data identitas Anda sudah lengkap.</p>
                </div>
            </div>
        @endif
    </div>

    {{-- FORM ALL-IN (Data & Media) --}}
    <form action="{{ url('/' . $user['nama_role'] . '/' . $user['uid'] . '/dashboard/my-profile/edit/process') }}"
        method="POST" enctype="multipart/form-data" x-data="profileHandler()">
        @csrf
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            {{-- SIDEBAR: Media Identitas --}}
            <div class="space-y-6">
                {{-- Foto Profil --}}
                <div class="bg-white border border-slate-200 rounded-[2.5rem] p-8 shadow-sm text-center">
                    <div class="relative w-40 h-40 mx-auto mb-6">
                        <img :src="avatarUrl"
                            class="w-full h-full rounded-[2.5rem] object-cover border-4 border-slate-50 shadow-xl transition-all duration-500"
                            :class="loadingAvatar ? 'opacity-50 blur-sm' : ''">

                        <button type="button" @click="$refs.avatarInput.click()"
                            class="absolute -bottom-2 -right-2 bg-white border border-slate-200 p-3 rounded-2xl shadow-xl hover:text-ksc-blue transition-all active:scale-95 group">
                            <i data-lucide="camera" class="w-5 h-5 text-slate-500 group-hover:text-ksc-blue"
                                x-show="!loadingAvatar"></i>
                            <i data-lucide="loader-2" class="w-5 h-5 animate-spin text-ksc-blue"
                                x-show="loadingAvatar"></i>
                        </button>

                        <input type="file" name="foto_profil" x-ref="avatarInput" class="hidden" accept="image/*"
                            @change="previewFile($event, 'avatar')">
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 uppercase tracking-tight">{{ $user['nama_lengkap'] }}</h3>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em] mt-1 italic">Identitas Utama</p>
                </div>

                {{-- Foto KTP --}}
                @if ($user['nama_role'] != 'admin')
                    <div class="bg-white border border-slate-200 rounded-[2.5rem] p-8 shadow-sm text-left">
                        <label class="block mb-4 text-[11px] font-black text-slate-400 uppercase tracking-widest">Verifikasi KTP</label>
                        <div class="relative w-full h-44 bg-slate-50 rounded-2xl border-2 border-dashed border-slate-200 overflow-hidden group cursor-pointer"
                            @click="$refs.ktpInput.click()">
                            <img :src="ktpUrl" x-show="ktpUrl" class="w-full h-full object-cover">
                            <div class="absolute inset-0 flex flex-col items-center justify-center bg-slate-900/40 opacity-0 group-hover:opacity-100 transition-opacity" x-show="ktpUrl">
                                <p class="text-white text-[10px] font-bold uppercase tracking-widest text-center px-4">Ganti Lampiran KTP</p>
                            </div>
                            <div class="flex flex-col items-center justify-center h-full space-y-2" x-show="!ktpUrl">
                                <i data-lucide="image-plus" class="w-8 h-8 text-slate-300"></i>
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-tighter">Unggah Foto KTP</p>
                            </div>
                        </div>
                        <input type="file" name="foto_ktp" x-ref="ktpInput" class="hidden" accept="image/*" @change="previewFile($event, 'ktp')">
                    </div>

                    {{-- Foto Akta --}}
                    <div class="bg-white border border-slate-200 rounded-[2.5rem] p-8 shadow-sm text-left">
                        <label class="block mb-4 text-[11px] font-black text-slate-400 uppercase tracking-widest">Foto Akta Kelahiran</label>
                        <div class="relative w-full h-44 bg-slate-50 rounded-2xl border-2 border-dashed border-slate-200 overflow-hidden group cursor-pointer"
                            @click="$refs.aktaInput.click()">
                            <img :src="aktaUrl" x-show="aktaUrl" class="w-full h-full object-cover">
                            <div class="absolute inset-0 flex flex-col items-center justify-center bg-slate-900/40 opacity-0 group-hover:opacity-100 transition-opacity" x-show="aktaUrl">
                                <p class="text-white text-[10px] font-bold uppercase tracking-widest text-center px-4">Ganti Foto Akta</p>
                            </div>
                            <div class="flex flex-col items-center justify-center h-full space-y-2" x-show="!aktaUrl">
                                <i data-lucide="file-text" class="w-8 h-8 text-slate-300"></i>
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-tighter">Unggah Foto Akta</p>
                            </div>
                        </div>
                        <input type="file" name="foto_akta" x-ref="aktaInput" class="hidden" accept="image/*" @change="previewFile($event, 'akta')">
                    </div>
                @endif
            </div>

            {{-- MAIN CONTENT: Panels --}}
            <div class="lg:col-span-2 space-y-8 text-left">
                
                {{-- SECTION 1: DATA PRIBADI --}}
                <div class="bg-white border border-slate-200 rounded-[2.5rem] shadow-sm overflow-hidden">
                    <div class="border-b border-slate-100 p-8 bg-slate-50/30 flex items-center gap-3">
                        <div class="w-10 h-10 bg-white rounded-xl shadow-sm flex items-center justify-center text-ksc-blue border border-slate-100">
                            <i data-lucide="user-cog" class="w-5 h-5"></i>
                        </div>
                        <h4 class="font-bold text-slate-900 uppercase tracking-tight">Data Personal & Kontak</h4>
                    </div>

                    <div class="p-8 grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2 bg-blue-50/50 p-4 rounded-2xl border border-blue-100 mb-2">
                             <label class="block mb-1 text-[10px] font-black text-slate-400 uppercase tracking-widest">Email Terdaftar (Akun)</label>
                             <p class="font-bold text-slate-700">{{ $user['email'] }}</p>
                        </div>
                        
                        <div>
                            <label class="block mb-2 text-[11px] font-black text-slate-400 uppercase tracking-widest">Nama Lengkap</label>
                            <input type="text" name="nama_lengkap" class="bg-slate-50 border border-slate-200 text-slate-900 text-sm font-bold rounded-2xl focus:ring-4 focus:ring-blue-50 focus:border-ksc-blue block w-full p-4 outline-none transition" value="{{ $user['nama_lengkap'] }}">
                        </div>

                        <div>
                            <label class="block mb-2 text-[11px] font-black text-slate-400 uppercase tracking-widest">Nama Panggilan</label>
                            <input type="text" name="nama_panggilan" class="bg-slate-50 border border-slate-200 text-slate-900 text-sm font-bold rounded-2xl focus:ring-4 focus:ring-blue-50 focus:border-ksc-blue block w-full p-4 outline-none transition" value="{{ $user['nama_panggilan'] ?? '' }}">
                        </div>

                        <div>
                            <label class="block mb-2 text-[11px] font-black text-slate-400 uppercase tracking-widest">Tempat Lahir</label>
                            <input type="text" name="tempat_lahir" class="bg-slate-50 border border-slate-200 text-slate-900 text-sm font-bold rounded-2xl focus:ring-4 focus:ring-blue-50 focus:border-ksc-blue block w-full p-4 outline-none transition" value="{{ $user['tempat_lahir'] ?? '' }}">
                        </div>

                        <div>
                            <label class="block mb-2 text-[11px] font-black text-slate-400 uppercase tracking-widest">Tanggal Lahir</label>
                            <input type="date" name="tanggal_lahir" class="bg-slate-50 border border-slate-200 text-slate-900 text-sm font-bold rounded-2xl focus:ring-4 focus:ring-blue-50 focus:border-ksc-blue block w-full p-4 outline-none transition" value="{{ $user['tanggal_lahir'] }}">
                        </div>

                        <div>
                            <label class="block mb-2 text-[11px] font-black text-slate-400 uppercase tracking-widest">Jenis Kelamin</label>
                            <select name="jenis_kelamin" class="bg-slate-50 border border-slate-200 text-slate-900 text-sm font-bold rounded-2xl focus:ring-4 focus:ring-blue-50 focus:border-ksc-blue block w-full p-4 outline-none transition">
                                <option value="L" {{ ($user['jenis_kelamin'] ?? '') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                                <option value="P" {{ ($user['jenis_kelamin'] ?? '') == 'P' ? 'selected' : '' }}>Perempuan</option>
                            </select>
                        </div>

                        <div>
                            <label class="block mb-2 text-[11px] font-black text-slate-400 uppercase tracking-widest">Nomor KTP (NIK)</label>
                            <input type="text" name="nomor_ktp" class="bg-slate-50 border border-slate-200 text-slate-900 text-sm font-bold rounded-2xl focus:ring-4 focus:ring-blue-50 focus:border-ksc-blue block w-full p-4 outline-none transition" value="{{ $user['nomor_ktp'] ?? '' }}" placeholder="16 digit Nomor Induk Kependudukan">
                        </div>

                        <div>
                            <label class="block mb-2 text-[11px] font-black text-slate-400 uppercase tracking-widest">Nomor KK</label>
                            <input type="text" name="nomor_kk" class="bg-slate-50 border border-slate-200 text-slate-900 text-sm font-bold rounded-2xl focus:ring-4 focus:ring-blue-50 focus:border-ksc-blue block w-full p-4 outline-none transition" value="{{ $user['nomor_kk'] ?? '' }}" placeholder="Nomor Kartu Keluarga">
                        </div>

                        <div>
                            <label class="block mb-2 text-[11px] font-black text-slate-400 uppercase tracking-widest">Nomor Telepon Utama</label>
                            <input type="text" name="no_telepon" class="bg-slate-50 border border-slate-200 text-slate-900 text-sm font-bold rounded-2xl focus:ring-4 focus:ring-blue-50 focus:border-ksc-blue block w-full p-4 outline-none transition" value="{{ $user['no_telepon'] }}">
                        </div>

                        <div>
                            <label class="block mb-2 text-[11px] font-black text-slate-400 uppercase tracking-widest">No. Telepon Darurat</label>
                            <input type="text" name="no_telepon_darurat" class="bg-slate-50 border border-slate-200 text-slate-900 text-sm font-bold rounded-2xl focus:ring-4 focus:ring-blue-50 focus:border-ksc-blue block w-full p-4 outline-none transition" value="{{ $user['no_telepon_darurat'] ?? '' }}">
                        </div>

                        <div class="md:col-span-1">
                            <label class="block mb-2 text-[11px] font-black text-slate-400 uppercase tracking-widest">Kota / Kabupaten</label>
                            <input type="text" name="kota" class="bg-slate-50 border border-slate-200 text-slate-900 text-sm font-bold rounded-2xl focus:ring-4 focus:ring-blue-50 focus:border-ksc-blue block w-full p-4 outline-none transition" value="{{ $user['kota'] ?? '' }}">
                        </div>

                        <div class="md:col-span-1">
                            <label class="block mb-2 text-[11px] font-black text-slate-400 uppercase tracking-widest">Provinsi</label>
                            <input type="text" name="provinsi" class="bg-slate-50 border border-slate-200 text-slate-900 text-sm font-bold rounded-2xl focus:ring-4 focus:ring-blue-50 focus:border-ksc-blue block w-full p-4 outline-none transition" value="{{ $user['provinsi'] ?? '' }}">
                        </div>

                        <div class="md:col-span-1">
                            <label class="block mb-2 text-[11px] font-black text-slate-400 uppercase tracking-widest">Kode Pos</label>
                            <input type="text" name="kode_pos" class="bg-slate-50 border border-slate-200 text-slate-900 text-sm font-bold rounded-2xl focus:ring-4 focus:ring-blue-50 focus:border-ksc-blue block w-full p-4 outline-none transition" value="{{ $user['kode_pos'] ?? '' }}">
                        </div>

                        <div class="md:col-span-2">
                            <label class="block mb-2 text-[11px] font-black text-slate-400 uppercase tracking-widest">Alamat Lengkap</label>
                            <textarea name="alamat_lengkap" rows="2" class="bg-slate-50 border border-slate-200 text-slate-900 text-sm font-bold rounded-2xl focus:ring-4 focus:ring-blue-50 focus:border-ksc-blue block w-full p-4 outline-none transition">{{ $user['alamat_lengkap'] ?? $user['alamat'] ?? '' }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- SECTION 2: DATA FISIK & KESEHATAN --}}
                <div class="bg-white border border-slate-200 rounded-[2.5rem] shadow-sm overflow-hidden">
                    <div class="border-b border-slate-100 p-8 bg-slate-50/30 flex items-center gap-3">
                        <div class="w-10 h-10 bg-white rounded-xl shadow-sm flex items-center justify-center text-red-500 border border-slate-100">
                            <i data-lucide="heart-pulse" class="w-5 h-5"></i>
                        </div>
                        <h4 class="font-bold text-slate-900 uppercase tracking-tight">Kesehatan & Fisik</h4>
                    </div>
                    <div class="p-8 grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="block mb-2 text-[11px] font-black text-slate-400 uppercase tracking-widest">Tinggi Badan (cm)</label>
                            <input type="number" step="0.1" name="tinggi_badan" class="bg-slate-50 border border-slate-200 text-slate-900 text-sm font-bold rounded-2xl focus:ring-4 focus:ring-blue-50 focus:border-ksc-blue block w-full p-4 outline-none" value="{{ $user['tinggi_badan'] ?? '' }}">
                        </div>
                        <div>
                            <label class="block mb-2 text-[11px] font-black text-slate-400 uppercase tracking-widest">Berat Badan (kg)</label>
                            <input type="number" step="0.1" name="berat_badan" class="bg-slate-50 border border-slate-200 text-slate-900 text-sm font-bold rounded-2xl focus:ring-4 focus:ring-blue-50 focus:border-ksc-blue block w-full p-4 outline-none" value="{{ $user['berat_badan'] ?? '' }}">
                        </div>
                        <div>
                            <label class="block mb-2 text-[11px] font-black text-slate-400 uppercase tracking-widest">Gol. Darah</label>
                            <select name="golongan_darah" class="bg-slate-50 border border-slate-200 text-slate-900 text-sm font-bold rounded-2xl focus:ring-4 focus:ring-blue-50 focus:border-ksc-blue block w-full p-4 outline-none">
                                <option value="" {{ empty($user['golongan_darah']) ? 'selected' : '' }}>Pilih</option>
                                @foreach(['A', 'B', 'AB', 'O'] as $gol)
                                <option value="{{ $gol }}" {{ ($user['golongan_darah'] ?? '') == $gol ? 'selected' : '' }}>{{ $gol }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="md:col-span-1">
                            <label class="block mb-2 text-[11px] font-black text-slate-400 uppercase tracking-widest">Alergi</label>
                            <input type="text" name="alergi" class="bg-slate-50 border border-slate-200 text-slate-900 text-sm font-bold rounded-2xl focus:ring-4 focus:ring-blue-50 block w-full p-4 outline-none" value="{{ $user['alergi'] ?? '' }}" placeholder="Tulis '-' jika tidak ada">
                        </div>
                        <div class="md:col-span-1">
                            <label class="block mb-2 text-[11px] font-black text-slate-400 uppercase tracking-widest">Obat Rutin</label>
                            <input type="text" name="obat_rutin" class="bg-slate-50 border border-slate-200 text-slate-900 text-sm font-bold rounded-2xl focus:ring-4 focus:ring-blue-50 block w-full p-4 outline-none" value="{{ $user['obat_rutin'] ?? '' }}" placeholder="Obat yang sering dikonsumsi">
                        </div>
                        <div class="md:col-span-1">
                            <label class="block mb-2 text-[11px] font-black text-slate-400 uppercase tracking-widest">Vaksin Covid</label>
                            <select name="vaksin_covid" class="bg-slate-50 border border-slate-200 text-slate-900 text-sm font-bold rounded-2xl focus:ring-4 focus:ring-blue-50 block w-full p-4 outline-none">
                                <option value="0" {{ ($user['vaksin_covid'] ?? 0) == 0 ? 'selected' : '' }}>Belum / Tidak Vaksin</option>
                                <option value="1" {{ ($user['vaksin_covid'] ?? 0) == 1 ? 'selected' : '' }}>Sudah Vaksin</option>
                            </select>
                        </div>
                        <div class="md:col-span-3">
                            <label class="block mb-2 text-[11px] font-black text-slate-400 uppercase tracking-widest">Riwayat Penyakit</label>
                            <textarea name="riwayat_penyakit" rows="2" class="bg-slate-50 border border-slate-200 text-slate-900 text-sm font-bold rounded-2xl focus:ring-4 focus:ring-blue-50 block w-full p-4 outline-none" placeholder="Tulis '-' jika tidak ada">{{ $user['riwayat_penyakit'] ?? '' }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- SECTION 3: DATA KELUARGA & PENDIDIKAN --}}
                <div class="bg-white border border-slate-200 rounded-[2.5rem] shadow-sm overflow-hidden">
                    <div class="border-b border-slate-100 p-8 bg-slate-50/30 flex items-center gap-3">
                        <div class="w-10 h-10 bg-white rounded-xl shadow-sm flex items-center justify-center text-emerald-500 border border-slate-100">
                            <i data-lucide="users" class="w-5 h-5"></i>
                        </div>
                        <h4 class="font-bold text-slate-900 uppercase tracking-tight">Keluarga & Pendidikan</h4>
                    </div>
                    <div class="p-8 grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block mb-2 text-[11px] font-black text-slate-400 uppercase tracking-widest">Nama Ayah</label>
                            <input type="text" name="nama_ayah" class="bg-slate-50 border border-slate-200 text-slate-900 text-sm font-bold rounded-2xl focus:ring-4 focus:ring-blue-50 block w-full p-4 outline-none" value="{{ $user['nama_ayah'] ?? '' }}">
                        </div>
                        <div>
                            <label class="block mb-2 text-[11px] font-black text-slate-400 uppercase tracking-widest">Pekerjaan Ayah</label>
                            <input type="text" name="pekerjaan_ayah" class="bg-slate-50 border border-slate-200 text-slate-900 text-sm font-bold rounded-2xl focus:ring-4 focus:ring-blue-50 block w-full p-4 outline-none" value="{{ $user['pekerjaan_ayah'] ?? '' }}">
                        </div>
                        <div>
                            <label class="block mb-2 text-[11px] font-black text-slate-400 uppercase tracking-widest">Nama Ibu</label>
                            <input type="text" name="nama_ibu" class="bg-slate-50 border border-slate-200 text-slate-900 text-sm font-bold rounded-2xl focus:ring-4 focus:ring-blue-50 block w-full p-4 outline-none" value="{{ $user['nama_ibu'] ?? '' }}">
                        </div>
                        <div>
                            <label class="block mb-2 text-[11px] font-black text-slate-400 uppercase tracking-widest">Pekerjaan Ibu</label>
                            <input type="text" name="pekerjaan_ibu" class="bg-slate-50 border border-slate-200 text-slate-900 text-sm font-bold rounded-2xl focus:ring-4 focus:ring-blue-50 block w-full p-4 outline-none" value="{{ $user['pekerjaan_ibu'] ?? '' }}">
                        </div>
                        <div>
                            <label class="block mb-2 text-[11px] font-black text-slate-400 uppercase tracking-widest">Asal Sekolah</label>
                            <input type="text" name="sekolah" class="bg-slate-50 border border-slate-200 text-slate-900 text-sm font-bold rounded-2xl focus:ring-4 focus:ring-blue-50 block w-full p-4 outline-none" value="{{ $user['sekolah'] ?? '' }}">
                        </div>
                        <div>
                            <label class="block mb-2 text-[11px] font-black text-slate-400 uppercase tracking-widest">Kelas</label>
                            <input type="text" name="kelas" class="bg-slate-50 border border-slate-200 text-slate-900 text-sm font-bold rounded-2xl focus:ring-4 focus:ring-blue-50 block w-full p-4 outline-none" value="{{ $user['kelas'] ?? '' }}">
                        </div>
                    </div>
                </div>

                {{-- SECTION 4: AFILIASI & KARIR --}}
                <div class="bg-white border border-slate-200 rounded-[2.5rem] shadow-sm overflow-hidden">
                    <div class="border-b border-slate-100 p-8 bg-slate-50/30 flex items-center gap-3">
                        <div class="w-10 h-10 bg-white rounded-xl shadow-sm flex items-center justify-center text-orange-500 border border-slate-100">
                            <i data-lucide="trophy" class="w-5 h-5"></i>
                        </div>
                        <h4 class="font-bold text-slate-900 uppercase tracking-tight">Afiliasi & Olahraga</h4>
                    </div>
                    <div class="p-8 grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <label class="block mb-2 text-[11px] font-black text-slate-400 uppercase tracking-widest">Klub Renang</label>
                            <select name="klub_renang" class="bg-slate-50 border border-slate-200 text-slate-900 text-sm font-bold rounded-2xl focus:ring-4 focus:ring-blue-50 block w-full p-4 outline-none transition appearance-none cursor-pointer">
                                <option value="" disabled {{ empty($user['klub_renang']) ? 'selected' : '' }}>Pilih Klub Renang</option>
                                @php
                                    $availableClubs = [
                                        'KHAFID SWIMMING CLUB', 'SIDOARJO AQUATIC CLUB', 'DELTA SWIMMING CLUB', 
                                        'JALASARI AQUATIC CLUB', 'FELLA SWIMMING', 'LAFI SWIMMING ACADEMY', 
                                        'SIDOARJO MUDA AQUATIC', 'OSCAR SWIMMING CLUB', 'HI-SIDOARJO AQUATIC', 
                                        'ELITE SWIMMING SIDOARJO', 'AL-FATH SWIMMING', 'DOLPHIN SWIMMING SIDOARJO'
                                    ];
                                @endphp
                                @foreach($availableClubs as $clubName)
                                    <option value="{{ $clubName }}" {{ ($user['klub_renang'] ?? '') == $clubName ? 'selected' : '' }}>{{ $clubName }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block mb-2 text-[11px] font-black text-slate-400 uppercase tracking-widest">Tingkat Keahlian</label>
                            <select name="tingkat_keahlian" class="bg-slate-50 border border-slate-200 text-slate-900 text-sm font-bold rounded-2xl focus:ring-4 focus:ring-blue-50 block w-full p-4 outline-none">
                                <option value="Pemula" {{ ($user['tingkat_keahlian'] ?? '') == 'Pemula' ? 'selected' : '' }}>Pemula</option>
                                <option value="Menengah" {{ ($user['tingkat_keahlian'] ?? '') == 'Menengah' ? 'selected' : '' }}>Menengah</option>
                                <option value="Mahir" {{ ($user['tingkat_keahlian'] ?? '') == 'Mahir' ? 'selected' : '' }}>Mahir</option>
                            </select>
                        </div>
                        <div>
                            <label class="block mb-2 text-[11px] font-black text-slate-400 uppercase tracking-widest">Pengalaman (Tahun)</label>
                            <input type="number" name="pengalaman_renang" class="bg-slate-50 border border-slate-200 text-slate-900 text-sm font-bold rounded-2xl focus:ring-4 focus:ring-blue-50 block w-full p-4 outline-none" value="{{ $user['pengalaman_renang'] ?? '' }}">
                        </div>
                        <div class="md:col-span-1">
                            <label class="block mb-2 text-[11px] font-black text-slate-400 uppercase tracking-widest">Nama Pelatih</label>
                            <input type="text" name="pelatih_renang" class="bg-slate-50 border border-slate-200 text-slate-900 text-sm font-bold rounded-2xl focus:ring-4 focus:ring-blue-50 block w-full p-4 outline-none" value="{{ $user['pelatih_renang'] ?? '' }}">
                        </div>
                        <div class="md:col-span-1">
                             <label class="block mb-2 text-[11px] font-black text-slate-400 uppercase tracking-widest">Jabatan di Klub</label>
                             <input type="text" name="jabatan_klub" class="bg-slate-50 border border-slate-200 text-slate-900 text-sm font-bold rounded-2xl focus:ring-4 focus:ring-blue-50 block w-full p-4 outline-none" value="{{ $user['jabatan_klub'] ?? '' }}">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block mb-2 text-[11px] font-black text-slate-400 uppercase tracking-widest">Prestasi Renang</label>
                            <textarea name="prestasi_renang" rows="2" class="bg-slate-50 border border-slate-200 text-slate-900 text-sm font-bold rounded-2xl focus:ring-4 focus:ring-blue-50 block w-full p-4 outline-none" placeholder="Sebutkan prestasi terbaik Anda">{{ $user['prestasi_renang'] ?? '' }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- SECTION 5: SECURITY --}}
                <div class="bg-white border border-slate-200 rounded-[2.5rem] shadow-sm overflow-hidden">
                    <div class="border-b border-slate-100 p-8 bg-slate-50/30 flex items-center gap-3">
                        <div class="w-10 h-10 bg-white rounded-xl shadow-sm flex items-center justify-center text-slate-600 border border-slate-100">
                            <i data-lucide="lock" class="w-5 h-5"></i>
                        </div>
                        <h4 class="font-bold text-slate-900 uppercase tracking-tight">Keamanan Akun</h4>
                    </div>
                    <div class="p-8">
                        <div>
                            <label class="block mb-2 text-[11px] font-black text-slate-400 uppercase tracking-widest">Ganti Password</label>
                            <input type="password" name="password" class="bg-slate-50 border border-slate-200 text-slate-900 text-sm rounded-2xl focus:ring-4 focus:ring-blue-50 block w-full p-4 outline-none transition" placeholder="Kosongkan jika tidak ingin mengubah password">
                        </div>
                    </div>
                </div>

                <div class="flex justify-end pt-4">
                    <button type="submit" class="bg-slate-900 hover:bg-black text-white px-10 py-5 rounded-2xl font-black text-xs transition shadow-2xl shadow-slate-300 flex items-center gap-3 uppercase tracking-[0.2em] transform hover:-translate-y-1 active:scale-95">
                        <i data-lucide="save" class="w-5 h-5 text-ksc-blue"></i>
                        Simpan Semua Perubahan
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    function profileHandler() {
        return {
            avatarUrl: `{{ $user['foto_profil'] ? url('/file/users/' . $user['foto_profil']) : 'https://ui-avatars.com/api/?name=' . urlencode($user['nama_lengkap']) . '&background=eff6ff&color=1e40af&size=256&bold=true' }}`,
            ktpUrl: `{{ $user['foto_ktp'] ? url('/file/id_cards/' . $user['foto_ktp']) : null }}`,
            aktaUrl: `{{ $user['foto_akta'] ? url('/file/birth_certificates/' . $user['foto_akta']) : null }}`,
            loadingAvatar: false,

            previewFile(event, type) {
                const file = event.target.files[0];
                if (!file) return;
                if (type === 'avatar') this.loadingAvatar = true;

                const reader = new FileReader();
                reader.onload = (e) => {
                    if (type === 'avatar') {
                        this.avatarUrl = e.target.result;
                        setTimeout(() => this.loadingAvatar = false, 400);
                    } else if (type === 'ktp') {
                        this.ktpUrl = e.target.result;
                    } else if (type === 'akta') {
                        this.aktaUrl = e.target.result;
                    }

                    setTimeout(() => { if (window.lucide) lucide.createIcons(); }, 500);
                };
                reader.readAsDataURL(file);
            }
        }
    }
</script>
@endsection