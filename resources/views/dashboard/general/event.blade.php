@extends('layouts.layout-dashboard.app')

@section('dashboard-section')
    <div class="p-4 md:p-8">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h2 class="text-2xl font-bold text-slate-900 leading-tight">Manajemen Event</h2>
                <p class="text-sm text-slate-500">Atur jadwal, lokasi, dan biaya event mendatang</p>
            </div>
            <button data-modal-target="modal-tambah-event" data-modal-toggle="modal-tambah-event"
                class="flex items-center gap-2 bg-ksc-blue hover:bg-ksc-dark text-white px-4 py-2.5 rounded-lg font-semibold transition shadow-sm focus:ring-4 focus:ring-blue-300"
                type="button">
                <i data-lucide="calendar-plus" class="w-5 h-5"></i>
                <span>Tambah Event</span>
            </button>
        </div>

        <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-slate-50 border-b border-slate-200">
                        <tr>
                            <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Banner</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-center">
                                Event</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Kategori</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Author</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Waktu & Tanggal
                            </th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-center">
                                Biaya</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Lokasi</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-center">
                                Status</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-center">Aksi
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100" id="event-table-body">
                        @foreach ($events['data'] as $event)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="px-6 py-4">
                                    @if ($event->banner_event === null)
                                        <img src="https://lh5.googleusercontent.com/proxy/t08n2HuxPfw8OpbutGWjekHAgxfPFv-pZZ5_-uTfhEGK8B5Lp-VN4VjrdxKtr8acgJA93S14m9NdELzjafFfy13b68pQ7zzDiAmn4Xg8LvsTw1jogn_7wStYeOx7ojx5h63Gliw"
                                            alt="Banner Event" class="w-16 h-10 object-cover rounded">
                                    @else
                                        <img src="{{ url('/file/banner-event/' . $event['banner_event']) }}"
                                            alt="Banner Event" class="w-16 h-10 object-cover rounded">
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-sm font-bold text-slate-900">{{ $event['nama_event'] }}</p>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-sm text-slate-700">{{ $event['nama_kategori'] }}</p>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-sm text-slate-700">{{ $event['author'] }}</p>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <p class="text-sm text-slate-900">
                                        {{ \Carbon\Carbon::parse($event['tanggal_event'])->translatedFormat('d M Y') }}</p>
                                    <p class="text-xs text-slate-500">
                                        {{ \Carbon\Carbon::parse($event['waktu_event'])->translatedFormat('H:i:s') }}</p>
                                </td>
                                <td class="px-6 py-4 text-center align-middle">
                                    <div class="flex flex-col items-center justify-center">

                                        @if ($event['tipe_event'] === 'gratis')
                                            <span class="text-xs font-bold text-green-600 bg-green-50 px-2 py-1 rounded">
                                                {{ $event['tipe_event'] }}
                                            </span>
                                        @else
                                            <span class="text-xs font-bold text-red-600 bg-red-50 px-2 py-1 rounded">
                                                {{ $event['tipe_event'] }}
                                            </span>
                                        @endif

                                        <p class="text-xs mt-1 font-bold text-slate-700">
                                            {{ $event['biaya_event'] }}
                                        </p>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-sm text-slate-700">{{ $event['lokasi_event'] }}</p>
                                </td>
                                @php
                                    $statusClass = match ($event['status_event']) {
                                        'berjalan' => 'bg-green-100 text-green-700 border-green-200',
                                        'ditunda' => 'bg-yellow-100 text-yellow-700 border-yellow-200',
                                        'ditutup' => 'bg-red-100 text-red-700 border-red-200',
                                        default => 'bg-gray-100 text-gray-700 border-gray-200',
                                    };
                                @endphp

                                <td class="px-6 py-4 text-center align-middle">
                                    <span
                                        class="px-3 py-1 text-[10px] font-bold lowercase rounded-full border {{ $statusClass }}">
                                        {{ strtolower($event['status_event']) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex justify-center gap-3">
                                        <button data-modal-target="modal-edit-event-{{ $event['uid'] }}"
                                            data-modal-toggle="modal-edit-event-{{ $event['uid'] }}"
                                            class="text-blue-600 hover:text-blue-800 transition p-1 hover:bg-blue-50 rounded">
                                            <i data-lucide="edit" class="w-5 h-5"></i>
                                        </button>
                                        <button data-modal-target="modal-hapus-event-{{ $event['uid'] }}"
                                            data-modal-toggle="modal-hapus-event-{{ $event['uid'] }}"
                                            class="text-red-600 hover:text-red-800 transition p-1 hover:bg-red-50 rounded">
                                            <i data-lucide="trash-2" class="w-5 h-5"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-6 flex flex-col md:flex-row items-center justify-between gap-4 px-2 pb-10">
            <div class="text-left">
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest">
                    Halaman <span class="text-slate-900">{{ $events['current_page'] }}</span>
                    dari <span class="text-slate-900">{{ $events['last_page'] }}</span>
                    — Total <span class="text-ksc-blue">{{ $events['total'] }}</span> Event
                </p>
            </div>

            <div class="flex items-center gap-2">
                @if ($events['current_page'] > 1)
                    <a href="{{ url('/' . $user['nama_role'] . '/dashboard/management-event/page/' . ($events['current_page'] - 1)) }}"
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

                <div class="flex items-center bg-white border border-slate-200 rounded-xl px-4 py-2.5 shadow-sm">
                    <span class="text-xs font-black text-slate-900">{{ $events['current_page'] }}</span>
                </div>

                @if ($events['current_page'] < $events['last_page'])
                    <a href="{{ url('/' . $user['nama_role'] . '/dashboard/management-event/page/' . ($events['current_page'] + 1)) }}"
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

    <div id="modal-tambah-event" tabindex="-1" aria-hidden="true"
        class="hidden fixed top-0 left-0 right-0 z-[70] w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-full max-h-full flex items-center justify-center bg-slate-900/60 backdrop-blur-sm transition-opacity">
        <div class="relative w-full max-w-4xl max-h-full">
            <div class="relative bg-white rounded-2xl shadow-2xl border border-slate-200 overflow-hidden">
                <div class="flex items-center justify-between p-4 md:p-6 border-b border-slate-100 bg-slate-50/50">
                    <div class="flex items-center gap-3">
                        <div
                            class="w-10 h-10 bg-ksc-blue rounded-xl flex items-center justify-center text-white shadow-lg shadow-ksc-blue/20">
                            <i data-lucide="calendar-plus" class="w-5 h-5"></i>
                        </div>
                        <h3 class="text-xl font-bold text-slate-900 uppercase tracking-tight">Buat Event Baru</h3>
                    </div>
                    <button type="button"
                        class="text-slate-400 hover:bg-slate-100 hover:text-slate-900 rounded-xl text-sm w-9 h-9 flex justify-center items-center transition"
                        data-modal-hide="modal-tambah-event">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>
                <form class="p-6 md:p-8 overflow-y-auto max-h-[75vh]"
                    action="{{ url('/' . $user['nama_role'] . '/' . $user['uid'] . '/dashboard/management-event/create/process') }}"
                    method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-left">
                        <div class="md:col-span-2">
                            <label class="block mb-2 text-sm font-bold text-slate-700 uppercase tracking-wider">Banner
                                Event</label>
                            <div class="flex items-center justify-center w-full">
                                <label for="banner-upload"
                                    class="flex flex-col items-center justify-center w-full h-52 border-2 border-slate-300 border-dashed rounded-2xl cursor-pointer bg-slate-50 hover:bg-slate-100 transition relative overflow-hidden group">
                                    <div id="preview-container-create" class="absolute inset-0 hidden">
                                        <img id="banner-preview-create" src="#" alt="Preview"
                                            class="w-full h-full object-cover">
                                        <div
                                            class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 flex items-center justify-center transition">
                                            <p class="text-white text-xs font-bold uppercase tracking-widest">Ganti Gambar
                                            </p>
                                        </div>
                                    </div>
                                    <div id="placeholder-content-create"
                                        class="flex flex-col items-center justify-center pt-5 pb-6">
                                        <i data-lucide="image-plus" class="w-10 h-10 text-slate-400 mb-3"></i>
                                        <p class="mb-2 text-sm text-slate-500 font-bold">Klik untuk unggah poster event</p>
                                        <p class="text-[10px] text-slate-400 uppercase font-medium">Format: WEBP, PNG, JPG
                                            (Maks. 2MB)</p>
                                    </div>
                                    <input id="banner-upload" name="banner_event" type="file" class="hidden"
                                        accept="image/*" onchange="previewBannerCreate(this)" />
                                </label>
                            </div>
                        </div>

                        <div class="md:col-span-2">
                            <label class="block mb-2 text-sm font-bold text-slate-700 uppercase tracking-wider">Nama
                                Event</label>
                            <input type="text" name="nama_event" value="{{ old('nama_event') }}"
                                class="bg-slate-50 border border-slate-300 text-slate-900 text-sm rounded-xl focus:ring-2 focus:ring-ksc-blue focus:border-ksc-blue block w-full p-3.5 outline-none"
                                placeholder="Contoh: KSC Fun Swimming 2026" required>
                        </div>

                        <div>
                            <label class="block mb-2 text-sm font-bold text-slate-700 uppercase tracking-wider">Kategori
                                Lomba</label>
                            <select name="uid_kategori"
                                class="bg-slate-50 border border-slate-300 text-slate-900 text-sm rounded-xl focus:ring-2 focus:ring-ksc-blue block w-full p-3.5 outline-none">
                                <option value="" disabled selected>-- Pilih Kategori --</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category['uid'] }}"
                                        {{ old('uid_kategori') === $category['uid'] ? 'selected' : '' }}>
                                        {{ $category['nama_kategori'] }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label
                                class="block mb-2 text-sm font-bold text-slate-700 uppercase tracking-wider">Penyelenggara</label>
                            <select name="uid_author"
                                class="bg-slate-50 border border-slate-300 text-slate-900 text-sm rounded-xl focus:ring-2 focus:ring-ksc-blue block w-full p-3.5 outline-none">
                                @foreach ($authors as $author)
                                    <option value="{{ $author['uid'] }}"
                                        {{ $user['uid'] === $author['uid'] ? 'selected' : '' }}>
                                        {{ $author['nama_lengkap'] }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="md:col-span-2">
                            <label class="block mb-2 text-sm font-bold text-slate-700 uppercase tracking-wider">Lokasi
                                Pertandingan</label>
                            <input type="text" name="lokasi_event" value="{{ old('lokasi_event') }}"
                                class="bg-slate-50 border border-slate-300 text-slate-900 text-sm rounded-xl focus:ring-2 focus:ring-ksc-blue block w-full p-3.5 outline-none"
                                placeholder="Nama Kolam / Alamat Lengkap" required>
                        </div>

                        <div>
                            <label class="block mb-2 text-sm font-bold text-slate-700 uppercase tracking-wider">Tanggal
                                Pelaksanaan</label>
                            <input type="date" name="tanggal_event" value="{{ old('tanggal_event') }}"
                                class="bg-slate-50 border border-slate-300 text-slate-900 text-sm rounded-xl block w-full p-3.5 outline-none focus:ring-2 focus:ring-blue-500"
                                required>
                        </div>

                        <div>
                            <label class="block mb-2 text-sm font-bold text-slate-700 uppercase tracking-wider">Waktu Mulai
                                (WIB)</label>
                            <input type="time" name="waktu_event" value="{{ old('waktu_event') }}"
                                class="bg-slate-50 border border-slate-300 text-slate-900 text-sm rounded-xl block w-full p-3.5 outline-none focus:ring-2 focus:ring-blue-500"
                                required>
                        </div>

                        <div>
                            <label class="block mb-2 text-sm font-bold text-slate-700 uppercase tracking-wider">Kuota
                                Peserta</label>
                            <input type="number" name="kuota_peserta" value="{{ old('kuota_peserta') }}"
                                min="1"
                                class="bg-slate-50 border border-slate-300 text-slate-900 text-sm rounded-xl block w-full p-3.5 outline-none focus:ring-2 focus:ring-blue-500"
                                required>
                        </div>

                        <div x-data="{ tipe: 'berbayar' }">
                            <label class="block mb-2 text-sm font-bold text-slate-700 uppercase tracking-wider">Tipe
                                Event</label>
                            <div class="grid grid-cols-2 gap-3">
                                <label
                                    class="flex items-center justify-center p-3.5 bg-slate-50 border border-slate-300 rounded-xl cursor-pointer hover:bg-slate-100 transition"
                                    :class="tipe === 'gratis' ? 'ring-2 ring-ksc-blue bg-blue-50 border-blue-200' : ''">
                                    <input type="radio" name="tipe_event" value="gratis" class="hidden"
                                        x-model="tipe">
                                    <span class="text-xs font-bold uppercase tracking-widest"
                                        :class="tipe === 'gratis' ? 'text-ksc-blue' : 'text-slate-500'">Gratis</span>
                                </label>
                                <label
                                    class="flex items-center justify-center p-3.5 bg-slate-50 border border-slate-300 rounded-xl cursor-pointer hover:bg-slate-100 transition"
                                    :class="tipe === 'berbayar' ? 'ring-2 ring-ksc-blue bg-blue-50 border-blue-200' : ''">
                                    <input type="radio" name="tipe_event" value="berbayar" class="hidden"
                                        x-model="tipe">
                                    <span class="text-xs font-bold uppercase tracking-widest"
                                        :class="tipe === 'berbayar' ? 'text-ksc-blue' : 'text-slate-500'">Berbayar</span>
                                </label>
                            </div>
                            <div x-show="tipe === 'berbayar'" x-transition class="mt-4">
                                <label
                                    class="block mb-2 text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">Nominal
                                    Biaya (IDR)</label>
                                <div class="relative mb-4">
                                    <div class="absolute left-4 top-1/2 -translate-y-1/2 font-bold text-slate-400 text-sm">
                                        Rp</div>
                                    <input type="number" name="biaya_event" value="{{ old('biaya_event') }}"
                                        class="bg-white border border-blue-200 text-slate-900 text-sm rounded-xl focus:ring-2 focus:ring-ksc-blue block w-full p-3.5 pl-12 outline-none shadow-sm"
                                        placeholder="Contoh: 150000" :required="tipe === 'berbayar'">
                                </div>
                                <label
                                    class="block mb-2 text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">Metode
                                    Pembayaran</label>
                                <select name="uid_payment_method"
                                    class="bg-white border border-blue-200 text-slate-900 text-sm rounded-xl focus:ring-2 focus:ring-ksc-blue block w-full p-3.5 outline-none shadow-sm"
                                    :required="tipe === 'berbayar'">
                                    <option value="" disabled selected>-- Pilih Rekening Pembayaran --</option>
                                    @foreach ($paymentMethods as $pm)
                                        <option value="{{ $pm['uid'] }}">{{ $pm['bank'] }} - {{ $pm['rekening'] }}
                                            (a.n {{ $pm['atas_nama'] }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="md:col-span-2">
                            <label class="block mb-2 text-sm font-bold text-slate-700 uppercase tracking-wider">Status
                                Publikasi</label>
                            <select name="status_event"
                                class="bg-slate-50 border border-slate-300 text-slate-900 text-sm rounded-xl focus:ring-2 focus:ring-ksc-blue block w-full p-3.5 outline-none">
                                <option value="berjalan">Berjalan (Registration Open)</option>
                                <option value="ditunda">Ditunda (Postponed)</option>
                                <option value="ditutup">Ditutup (Registration Closed)</option>
                            </select>
                        </div>

                        <div class="md:col-span-2">
                            <label class="block mb-2 text-sm font-bold text-slate-700 uppercase tracking-wider">Deskripsi &
                                Peraturan</label>
                            <div
                                class="bg-slate-50 border border-slate-300 rounded-2xl overflow-hidden shadow-inner font-sans">
                                <div id="editor-create-container" class="h-56 bg-white text-left"></div>
                                <input type="hidden" name="deskripsi" id="deskripsi-input">
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center pt-8 mt-6 border-t border-slate-100 space-x-3 justify-end">
                        <button data-modal-hide="modal-tambah-event" type="button"
                            class="text-slate-500 bg-white hover:bg-slate-50 rounded-xl border border-slate-200 text-sm font-bold px-8 py-3 transition">Batal</button>
                        <button type="submit"
                            class="text-white bg-slate-900 hover:bg-black font-bold rounded-xl text-sm px-10 py-3 shadow-xl transition-all">Simpan
                            & Terbitkan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @foreach ($events['data'] as $event)
        <div id="modal-edit-event-{{ $event['uid'] }}" tabindex="-1" aria-hidden="true"
            class="hidden fixed top-0 left-0 right-0 z-[70] w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-full max-h-full flex items-center justify-center bg-slate-900/60 backdrop-blur-sm transition-opacity">
            <div class="relative w-full max-w-4xl max-h-full">
                <div class="relative bg-white rounded-2xl shadow-2xl border border-slate-200 overflow-hidden">
                    <div class="flex items-center justify-between p-4 md:p-6 border-b border-slate-100 bg-slate-50/50">
                        <div class="flex items-center gap-3">
                            <div
                                class="w-10 h-10 bg-ksc-blue rounded-xl flex items-center justify-center text-white shadow-lg shadow-ksc-blue/20">
                                <i data-lucide="edit-3" class="w-5 h-5"></i>
                            </div>
                            <h3 class="text-xl font-bold text-slate-900 uppercase tracking-tight">Edit Data Event</h3>
                        </div>
                        <button type="button"
                            class="text-slate-400 hover:bg-slate-100 hover:text-slate-900 rounded-xl text-sm w-9 h-9 flex justify-center items-center transition"
                            data-modal-hide="modal-edit-event-{{ $event['uid'] }}">
                            <i data-lucide="x" class="w-5 h-5"></i>
                        </button>
                    </div>

                    <form id="form-edit-event" class="p-6 md:p-8 overflow-y-auto max-h-[75vh]"
                        action="{{ url('/' . $user['nama_role'] . '/' . $user['uid'] . '/' . $event['uid'] . '/dashboard/management-event/edit/process') }}"
                        method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-left">

                            <div class="md:col-span-2">
                                <label
                                    class="block mb-2 text-sm font-bold text-slate-700 uppercase tracking-wider text-left">
                                    Banner Event (Klik untuk Ubah)
                                </label>
                                <div class="flex items-center justify-center w-full">
                                    <label for="edit-banner-upload-{{ $event['uid'] }}"
                                        class="flex flex-col items-center justify-center w-full h-52 border-2 border-slate-300 border-dashed rounded-2xl cursor-pointer bg-slate-50 hover:bg-slate-100 transition relative overflow-hidden group">

                                        <div id="edit-preview-container-{{ $event['uid'] }}" class="absolute inset-0">
                                            <img id="edit-banner-preview-{{ $event['uid'] }}"
                                                src="{{ $event['banner_event'] == null ? url('/file/dummy/dummy.webp') : url('/file/banner-event/' . $event['banner_event']) }}"
                                                alt="Preview" class="w-full h-full object-cover">

                                            <div
                                                class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 flex items-center justify-center transition">
                                                <p class="text-white text-xs font-bold uppercase tracking-widest">Ganti
                                                    Gambar</p>
                                            </div>
                                        </div>

                                        <input id="edit-banner-upload-{{ $event['uid'] }}" name="banner_event"
                                            type="file" class="hidden" accept="image/*"
                                            onchange="previewEditBanner(this, '{{ $event['uid'] }}')" />
                                    </label>
                                </div>
                            </div>

                            <div class="md:col-span-2">
                                <label
                                    class="block mb-2 text-sm font-bold text-slate-700 uppercase tracking-wider text-left">Nama
                                    Event</label>
                                <input type="text" name="nama_event" id="edit_nama_event"
                                    value="{{ $event['nama_event'] }}"
                                    class="bg-slate-50 border border-slate-300 text-slate-900 text-sm rounded-xl focus:ring-2 focus:ring-ksc-blue focus:border-ksc-blue block w-full p-3.5 outline-none"
                                    required>
                            </div>

                            <div>
                                <label
                                    class="block mb-2 text-sm font-bold text-slate-700 uppercase tracking-wider">Kategori
                                    Lomba</label>
                                <select name="uid_kategori" id="edit_uid_kategori"
                                    class="bg-slate-50 border border-slate-300 text-slate-900 text-sm rounded-xl focus:ring-2 focus:ring-ksc-blue block w-full p-3.5 outline-none">
                                    @foreach ($categories as $category)
                                        <option value="{{ $category['uid'] }}"
                                            {{ $event['nama_kategori'] == $category['nama_kategori'] ? 'selected' : '' }}>
                                            {{ $category['nama_kategori'] }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label
                                    class="block mb-2 text-sm font-bold text-slate-700 uppercase tracking-wider">Penyelenggara</label>
                                <select name="uid_author" id="edit_uid_author"
                                    class="bg-slate-50 border border-slate-300 text-slate-900 text-sm rounded-xl focus:ring-2 focus:ring-ksc-blue block w-full p-3.5 outline-none">
                                    @foreach ($authors as $author)
                                        <option value="{{ $author['uid'] }}"
                                            {{ $event['author'] == $author['nama_lengkap'] ? 'selected' : '' }}>
                                            {{ $author['nama_lengkap'] }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="md:col-span-2">
                                <label class="block mb-2 text-sm font-bold text-slate-700 uppercase tracking-wider">Lokasi
                                    Pertandingan</label>
                                <input type="text" name="lokasi_event" id="edit_lokasi_event"
                                    value="{{ $event['lokasi_event'] }}"
                                    class="bg-slate-50 border border-slate-300 text-slate-900 text-sm rounded-xl focus:ring-2 focus:ring-ksc-blue block w-full p-3.5 outline-none"
                                    required>
                            </div>

                            <div>
                                <label class="block mb-2 text-sm font-bold text-slate-700 uppercase tracking-wider">Tanggal
                                    Pelaksanaan</label>
                                <input type="date" name="tanggal_event" id="edit_tanggal_event"
                                    value="{{ $event['tanggal_event'] }}"
                                    class="bg-slate-50 border border-slate-300 text-slate-900 text-sm rounded-xl block w-full p-3.5 outline-none focus:ring-2 focus:ring-ksc-blue"
                                    required>
                            </div>
                            <div>
                                <label class="block mb-2 text-sm font-bold text-slate-700 uppercase tracking-wider">Waktu
                                    Mulai</label>
                                <input type="time" name="waktu_event" id="edit_waktu_event"
                                    value="{{ $event['waktu_event'] }}"
                                    class="bg-slate-50 border border-slate-300 text-slate-900 text-sm rounded-xl block w-full p-3.5 outline-none focus:ring-2 focus:ring-ksc-blue"
                                    required>
                            </div>

                            <div>
                                <label class="block mb-2 text-sm font-bold text-slate-700 uppercase tracking-wider">
                                    Kuota Peserta
                                </label>
                                <input type="number" name="kuota_peserta" value="{{ $event['kuota_peserta'] }}"
                                    min="1"
                                    class="bg-slate-50 border border-slate-300 text-slate-900 text-sm rounded-xl block w-full p-3.5 outline-none focus:ring-2 focus:ring-ksc-blue"
                                    required>
                            </div>

                            <div x-data="{ tipeEdit: `{{ $event['tipe_event'] }}` }" id="edit-tipe-container">
                                <label class="block mb-2 text-sm font-bold text-slate-700 uppercase tracking-wider">Tipe
                                    Event</label>
                                <div class="grid grid-cols-2 gap-3">
                                    <label
                                        class="flex items-center justify-center p-3.5 bg-slate-50 border border-slate-300 rounded-xl cursor-pointer hover:bg-slate-100 transition"
                                        :class="tipeEdit === 'gratis' ? 'ring-2 ring-ksc-blue bg-blue-50 border-blue-200' :
                                            ''">
                                        <input type="radio" name="tipe_event" value="gratis" class="hidden"
                                            x-model="tipeEdit" id="edit_tipe_gratis">
                                        <span class="text-xs font-bold uppercase tracking-widest"
                                            :class="tipeEdit === 'gratis' ? 'text-ksc-blue' : 'text-slate-500'">Gratis</span>
                                    </label>
                                    <label
                                        class="flex items-center justify-center p-3.5 bg-slate-50 border border-slate-300 rounded-xl cursor-pointer hover:bg-slate-100 transition"
                                        :class="tipeEdit === 'berbayar' ? 'ring-2 ring-ksc-blue bg-blue-50 border-blue-200' :
                                            ''">
                                        <input type="radio" name="tipe_event" value="berbayar" class="hidden"
                                            x-model="tipeEdit" id="edit_tipe_berbayar">
                                        <span class="text-xs font-bold uppercase tracking-widest"
                                            :class="tipeEdit === 'berbayar' ? 'text-ksc-blue' : 'text-slate-500'">Berbayar</span>
                                    </label>
                                </div>
                                <div x-show="tipeEdit === 'berbayar'" x-transition class="mt-4">
                                    <label
                                        class="block mb-2 text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">Nominal
                                        Biaya (IDR)</label>
                                    <div class="relative mb-4">
                                        <div
                                            class="absolute left-4 top-1/2 -translate-y-1/2 font-bold text-slate-400 text-sm">
                                            Rp</div>
                                        <input type="number" name="biaya_event" id="edit_biaya_event"
                                            value="{{ $event['biaya_event'] }}"
                                            class="bg-white border border-blue-200 text-slate-900 text-sm rounded-xl focus:ring-2 focus:ring-ksc-blue block w-full p-3.5 pl-12 outline-none shadow-sm">
                                    </div>

                                    <label
                                        class="block mb-2 text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">Metode
                                        Pembayaran</label>
                                    <select name="uid_payment_method"
                                        class="bg-white border border-blue-200 text-slate-900 text-sm rounded-xl focus:ring-2 focus:ring-ksc-blue block w-full p-3.5 outline-none shadow-sm"
                                        :required="tipeEdit === 'berbayar'">
                                        <option value="" disabled
                                            {{ !$event['uid_payment_method'] ? 'selected' : '' }}>-- Pilih Rekening
                                            Pembayaran --</option>
                                        @foreach ($paymentMethods as $pm)
                                            <option value="{{ $pm['uid'] }}"
                                                {{ $event['uid_payment_method'] == $pm['uid'] ? 'selected' : '' }}>
                                                {{ $pm['bank'] }} - {{ $pm['rekening'] }} (a.n {{ $pm['atas_nama'] }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="md:col-span-2">
                                <label class="block mb-2 text-sm font-bold text-slate-700 uppercase tracking-wider">Status
                                    Publikasi</label>
                                <select name="status_event" id="edit_status_event"
                                    class="bg-slate-50 border border-slate-300 text-slate-900 text-sm rounded-xl focus:ring-2 focus:ring-ksc-blue block w-full p-3.5 outline-none">
                                    <option {{ $event['status_event'] === 'berjalan' ? 'selected' : '' }}
                                        value="berjalan">
                                        Berjalan (Registration Open)</option>
                                    <option {{ $event['status_event'] === 'ditunda' ? 'selected' : '' }} value="ditunda">
                                        Ditunda (Postponed)</option>
                                    <option {{ $event['status_event'] === 'ditutup' ? 'selected' : '' }} value="ditutup">
                                        Ditutup (Registration Closed)</option>
                                </select>
                            </div>

                            <div class="md:col-span-2">
                                <label class="block mb-2 text-sm font-bold text-slate-700 uppercase tracking-wider">
                                    Deskripsi & Peraturan
                                </label>
                                <div
                                    class="bg-slate-50 border border-slate-300 rounded-2xl overflow-hidden shadow-inner font-sans">
                                    <div id="edit-editor-{{ $event['uid'] }}" class="h-56 bg-white text-left">
                                        {!! $event['deskripsi'] !!}
                                    </div>
                                    <input type="hidden" name="deskripsi" id="edit-deskripsi-input-{{ $event['uid'] }}"
                                        value="{{ $event['deskripsi'] }}">
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center pt-8 mt-6 border-t border-slate-100 space-x-3 justify-end">
                            <button data-modal-hide="modal-edit-event-{{ $event['uid'] }}" type="button"
                                class="text-slate-500 bg-white hover:bg-slate-50 rounded-xl border border-slate-200 text-sm font-bold px-8 py-3 transition">Batal</button>
                            <button type="submit"
                                class="text-white bg-slate-900 hover:bg-black font-bold rounded-xl text-sm px-10 py-3 shadow-xl transition-all">Simpan
                                Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>


        <div id="modal-hapus-event-{{ $event['uid'] }}" tabindex="-1" aria-hidden="true"
            class="hidden fixed top-0 left-0 right-0 z-[80] w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-full max-h-full flex items-center justify-center bg-slate-900/60 backdrop-blur-sm">
            <div class="relative w-full max-w-md max-h-full">
                <div class="relative bg-white rounded-2xl shadow-2xl p-6 text-center">
                    <div
                        class="w-20 h-20 bg-red-100 text-red-600 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i data-lucide="alert-triangle" class="w-10 h-10"></i>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-2">Hapus Event?</h3>
                    <p class="text-slate-500 text-sm mb-6">Tindakan ini tidak dapat dibatalkan. Seluruh data pendaftar pada
                        event ini juga akan terhapus.</p>

                    <form id="form-hapus-event" method="POST"
                        action="{{ url('/' . $user['nama_role'] . '/' . $user['uid'] . '/' . $event['uid'] . '/dashboard/management-event/delete/process') }}">
                        @csrf
                        <div class="flex gap-3">
                            <button type="button" data-modal-hide="modal-hapus-event-{{ $event['uid'] }}"
                                class="flex-1 px-4 py-3 bg-slate-100 text-slate-600 rounded-xl font-bold hover:bg-slate-200 transition">Batal</button>
                            <button type="submit"
                                class="flex-1 px-4 py-3 bg-red-600 text-white rounded-xl font-bold hover:bg-red-700 shadow-lg shadow-red-200 transition">Ya,
                                Hapus</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach

    <script>
        // *** FUNGSI UNTUK PREVIEW BANNER ***
        function previewBannerCreate(input) {
            const preview = document.getElementById('banner-preview-create');
            const container = document.getElementById('preview-container-create');
            const placeholder = document.getElementById('placeholder-content-create');
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    container.classList.remove('hidden');
                    placeholder.classList.add('hidden');
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        function previewEditBanner(input, uid) {
            const preview = document.getElementById(`edit-banner-preview-${uid}`);

            if (input.files && input.files[0]) {
                const reader = new FileReader();

                reader.onload = function(e) {
                    preview.classList.add('opacity-0');

                    setTimeout(() => {
                        preview.src = e.target.result;
                        preview.classList.remove('opacity-0');
                        preview.classList.add('animate-in', 'fade-in', 'zoom-in', 'duration-500');
                    }, 100);
                }

                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // 1. Inisialisasi Editor untuk "Create" (Jika ada)
            const createContainer = document.querySelector('#editor-create-container');
            if (createContainer) {
                const quillCreate = new Quill('#editor-create-container', {
                    theme: 'snow',
                    placeholder: 'Masukkan deskripsi event...',
                    modules: {
                        toolbar: [
                            ['bold', 'italic', 'underline'],
                            [{
                                'list': 'ordered'
                            }, {
                                'list': 'bullet'
                            }],
                            ['link', 'blockquote', 'clean']
                        ]
                    }
                });

                quillCreate.on('text-change', function() {
                    const input = document.querySelector('#deskripsi-input');
                    if (input) input.value = quillCreate.root.innerHTML;
                });
            }

            // 2. Inisialisasi Editor untuk "Edit" (Looping)
            const toolbarOptions = [
                ['bold', 'italic', 'underline'],
                [{
                    'list': 'ordered'
                }, {
                    'list': 'bullet'
                }],
                ['link', 'blockquote', 'clean']
            ];

            @foreach ($events['data'] as $event)
                (function() {
                    const uid = "{{ $event['uid'] }}";
                    const selector = `#edit-editor-${uid}`;
                    const inputSelector = `#edit-deskripsi-input-${uid}`;

                    if (document.querySelector(selector)) {
                        const quillEdit = new Quill(selector, {
                            theme: 'snow',
                            placeholder: 'Edit peraturan event...',
                            modules: {
                                toolbar: toolbarOptions
                            }
                        });

                        // Set konten awal (Jika belum otomatis terisi dari HTML)
                        // quillEdit.root.innerHTML = `{!! $event['deskripsi'] !!}`;

                        quillEdit.on('text-change', function() {
                            const targetInput = document.querySelector(inputSelector);
                            if (targetInput) targetInput.value = quillEdit.root.innerHTML;
                        });
                    }
                })();
            @endforeach
        });

        // 3. Global Preview Functions (Satu saja cukup)
        function previewEditBanner(input, uid) {
            const preview = document.getElementById(`edit-banner-preview-${uid}`);
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.classList.add('opacity-0');
                    setTimeout(() => {
                        preview.src = e.target.result;
                        preview.classList.remove('opacity-0');
                        preview.classList.add('animate-in', 'fade-in', 'zoom-in', 'duration-500');
                    }, 100);
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
@endsection
