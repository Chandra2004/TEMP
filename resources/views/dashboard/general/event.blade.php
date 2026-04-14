@extends('layouts.layout-dashboard.app')

@section('dashboard-section')
    <div class="p-4 md:p-8">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h2 class="text-2xl font-bold text-slate-900 leading-tight">Manajemen Event</h2>
                <p class="text-sm text-slate-500">Atur jadwal, lokasi, dan biaya event mendatang</p>
            </div>
            @if ($user->can('manage-events'))
                <button data-modal-target="modal-tambah-event" data-modal-toggle="modal-tambah-event"
                    class="flex items-center gap-2 bg-ksc-blue hover:bg-ksc-dark text-white px-4 py-2.5 rounded-lg font-semibold transition shadow-sm focus:ring-4 focus:ring-blue-300"
                    type="button">
                    <i data-lucide="calendar-plus" class="w-5 h-5"></i>
                    <span>Tambah Event</span>
                </button>
            @endif
        </div>

        <div class="bg-white rounded-3xl border border-slate-100 shadow-xl shadow-slate-200/50 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-slate-50/50 border-b border-slate-100">
                            <th scope="col" class="px-6 py-4 font-black text-[10px] text-slate-400 uppercase tracking-widest">Event</th>
                            <th scope="col" class="px-6 py-4 font-black text-[10px] text-slate-400 uppercase tracking-widest">Dates</th>
                            <th scope="col" class="px-6 py-4 font-black text-[10px] text-slate-400 uppercase tracking-widest text-center">Lanes</th>
                            <th scope="col" class="px-6 py-4 font-black text-[10px] text-slate-400 uppercase tracking-widest text-center">Status</th>
                            @if($user->can('manage-events') || $user->can('view-reports'))
                            <th scope="col" class="px-6 py-4 font-black text-[10px] text-slate-400 uppercase tracking-widest text-center">Actions</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @foreach ($events['data'] as $event)
                            <tr class="hover:bg-slate-50/50 transition-colors group">
                                <td class="px-6 py-5">
                                    <div class="flex items-center gap-4">
                                        <div class="w-14 h-10 rounded-xl overflow-hidden shadow-sm flex-shrink-0">
                                            <img src="{{ $event['banner_event'] == null ? url('/file/dummy/dummy.webp') : url('/file/banner-event/' . $event['banner_event']) }}"
                                                alt="Banner" class="w-full h-full object-cover">
                                        </div>
                                        <div>
                                            <p class="font-bold text-slate-900 group-hover:text-blue-600 transition truncate max-w-[200px]">
                                                {{ $event['nama_event'] }}</p>
                                            <p class="text-[10px] font-medium text-slate-400 uppercase tracking-wider mt-0.5">
                                                {{ $event['lokasi_event'] }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-5">
                                    <div class="flex flex-col">
                                        <span class="text-xs font-bold text-slate-700">
                                            {{ date('d M Y', strtotime($event['tanggal_mulai'])) }}
                                        </span>
                                        <span class="text-[10px] font-medium text-slate-400">
                                            s/d {{ date('d M Y', strtotime($event['tanggal_selesai'])) }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-5 text-center">
                                    <span class="text-sm font-black text-slate-900">{{ $event['jumlah_lintasan'] }}</span>
                                </td>
                                <td class="px-6 py-5">
                                    <div class="flex justify-center">
                                        @php
                                            $statusConfig = match ($event['status_event']) {
                                                'berjalan' => ['bg' => 'bg-green-50', 'text' => 'text-green-600', 'border' => 'border-green-100'],
                                                'ditunda' => ['bg' => 'bg-amber-50', 'text' => 'text-amber-600', 'border' => 'border-amber-100'],
                                                'ditutup' => ['bg' => 'bg-rose-50', 'text' => 'text-rose-600', 'border' => 'border-rose-100'],
                                                default => ['bg' => 'bg-slate-50', 'text' => 'text-slate-600', 'border' => 'border-slate-100'],
                                            };
                                        @endphp
                                        <span class="px-2.5 py-1 {{ $statusConfig['bg'] }} {{ $statusConfig['text'] }} {{ $statusConfig['border'] }} border rounded-lg text-[9px] font-black uppercase tracking-widest">
                                            {{ $event['status_event'] }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-5">
                                    <div class="flex items-center justify-center gap-2">
                                        @if ($user->can('view-reports'))
                                            <a href="{{ url('/' . $user['nama_role'] . '/dashboard/management-event/' . $event['uid'] . '/export-buku-acara') }}"
                                                target="_blank"
                                                class="p-2 bg-emerald-50 text-emerald-600 rounded-xl hover:bg-emerald-600 hover:text-white transition shadow-sm active:scale-90"
                                                title="Export Buku Acara">
                                                <i data-lucide="printer" class="w-4 h-4"></i>
                                            </a>
                                            <a href="{{ url('/' . $user['nama_role'] . '/dashboard/management-event/' . $event['uid'] . '/export-buku-hasil') }}"
                                                target="_blank"
                                                class="p-2 bg-blue-50 text-blue-600 rounded-xl hover:bg-blue-600 hover:text-white transition shadow-sm active:scale-90"
                                                title="Export Buku Hasil">
                                                <i data-lucide="award" class="w-4 h-4"></i>
                                            </a>
                                        @endif

                                        @if ($user->can('manage-events'))
                                            <button data-modal-target="modal-edit-event-{{ $event['uid'] }}"
                                                data-modal-toggle="modal-edit-event-{{ $event['uid'] }}"
                                                class="p-2 bg-blue-50 text-blue-600 rounded-xl hover:bg-blue-600 hover:text-white transition shadow-sm active:scale-90">
                                                <i data-lucide="edit-3" class="w-4 h-4"></i>
                                            </button>
                                            <button data-modal-target="modal-hapus-event-{{ $event['uid'] }}"
                                                data-modal-toggle="modal-hapus-event-{{ $event['uid'] }}"
                                                class="p-2 bg-rose-50 text-rose-600 rounded-xl hover:bg-rose-600 hover:text-white transition shadow-sm active:scale-90">
                                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                                            </button>
                                        @else
                                            <a href="{{ url('/detail-event/' . $event['slug'] . '/' . $event['uid']) }}" 
                                                class="px-4 py-1.5 bg-ksc-blue text-white rounded-lg text-xs font-bold hover:bg-ksc-dark transition shadow-sm shadow-blue-200">
                                                Detail & Daftar
                                            </a>
                                        @endif
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

    @if ($user->can('manage-events'))
    <div id="modal-tambah-event" tabindex="-1" aria-hidden="true"
        class="hidden fixed top-0 left-0 right-0 z-[70] w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-full max-h-full items-center justify-center bg-slate-900/60 backdrop-blur-sm transition-opacity">
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
                    method="POST" enctype="multipart/form-data" x-data="{
                        tipe: 'berbayar',
                        categories: {{ json_encode($categories) }},
                        master_params: {{ json_encode($requirement_parameters) }},
                        matches: [{
                            uid_category: '',
                            nama_acara: '',
                            tipe_biaya: 'berbayar',
                            biaya_pendaftaran: 0,
                            jumlah_seri: 1,
                            waktu_mulai: '08:00',
                            requirements: []
                        }],
                        addMatch() {
                            this.matches.push({
                                uid_category: '',
                                nama_acara: '',
                                tipe_biaya: 'berbayar',
                                biaya_pendaftaran: 0,
                                jumlah_seri: 1,
                                waktu_mulai: '08:00',
                                requirements: []
                            });
                        },
                        updateMatchName(index) {
                            const cat = this.categories.find(c => c.uid === this.matches[index].uid_category);
                            if (cat) {
                                this.matches[index].nama_acara = cat.nama_kategori;
                            }
                        },
                        removeMatch(index) {
                            this.matches.splice(index, 1);
                        },
                        addRequirement(matchIndex) {
                            this.matches[matchIndex].requirements.push({
                                parameter_name: '',
                                operator: '=',
                                parameter_value: '',
                                input_type: 'text',
                                options: []
                            });
                        },
                        removeRequirement(matchIndex, reqIndex) {
                            this.matches[matchIndex].requirements.splice(reqIndex, 1);
                        },
                        onParamChange(mIdx, rIdx) {
                            const pName = this.matches[mIdx].requirements[rIdx].parameter_name;
                            const param = this.master_params.find(p => p.parameter_key === pName);
                            if (param) {
                                this.matches[mIdx].requirements[rIdx].input_type = param.input_type;
                                this.matches[mIdx].requirements[rIdx].options = JSON.parse(param.input_options || '[]');
                                this.matches[mIdx].requirements[rIdx].allowed_operators = JSON.parse(param.allowed_operators || '[]');
                                // Reset operator if current one not allowed
                                if (!this.matches[mIdx].requirements[rIdx].allowed_operators.includes(this.matches[mIdx].requirements[rIdx].operator)) {
                                    this.matches[mIdx].requirements[rIdx].operator = this.matches[mIdx].requirements[rIdx].allowed_operators[0] || '=';
                                }
                            }
                        }
                    }">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-left">
                        <div class="md:col-span-2">
                            <label class="block mb-2 text-sm font-bold text-slate-700 uppercase tracking-wider">Banner Event</label>
                            <div class="flex items-center justify-center w-full">
                                <label for="banner-upload"
                                    class="flex flex-col items-center justify-center w-full h-52 border-2 border-slate-300 border-dashed rounded-2xl cursor-pointer bg-slate-50 hover:bg-slate-100 transition relative overflow-hidden group">
                                    <div id="preview-container-create" class="absolute inset-0 hidden">
                                        <img id="banner-preview-create" src="#" alt="Preview"
                                            class="w-full h-full object-cover">
                                        <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 flex items-center justify-center transition">
                                            <p class="text-white text-xs font-bold uppercase tracking-widest">Ganti Gambar</p>
                                        </div>
                                    </div>
                                    <div id="placeholder-content-create" class="flex flex-col items-center justify-center pt-5 pb-6">
                                        <i data-lucide="image-plus" class="w-10 h-10 text-slate-400 mb-3"></i>
                                        <p class="mb-2 text-sm text-slate-500 font-bold">Klik untuk unggah poster event</p>
                                        <p class="text-[10px] text-slate-400 uppercase font-medium">Format: WEBP, PNG, JPG (Maks. 2MB)</p>
                                    </div>
                                    <input id="banner-upload" name="banner_event" type="file" class="hidden" accept="image/*" onchange="previewBannerCreate(this)" />
                                </label>
                            </div>
                        </div>

                        <div>
                            <label class="block mb-2 text-sm font-bold text-slate-700 uppercase tracking-wider">Logo Kiri (Header Report)</label>
                            <div class="flex items-center justify-center w-full">
                                <label for="logo-kiri-upload" class="flex flex-col items-center justify-center w-full h-32 border-2 border-slate-300 border-dashed rounded-2xl cursor-pointer bg-slate-50 hover:bg-slate-100 transition relative overflow-hidden group">
                                    <div id="preview-logo-kiri-create" class="absolute inset-0 hidden">
                                        <img id="logo-kiri-preview-img" src="#" alt="Preview" class="w-full h-full object-contain p-2">
                                    </div>
                                    <div id="placeholder-logo-kiri-create" class="flex flex-col items-center justify-center">
                                        <i data-lucide="image" class="w-6 h-6 text-slate-400 mb-1"></i>
                                        <p class="text-[10px] text-slate-500 font-bold uppercase">Unggah Logo</p>
                                    </div>
                                    <input id="logo-kiri-upload" name="logo_kiri" type="file" class="hidden" accept="image/*" onchange="previewLogo(this, 'logo-kiri-preview-img', 'preview-logo-kiri-create', 'placeholder-logo-kiri-create')" />
                                </label>
                            </div>
                        </div>

                        <div>
                            <label class="block mb-2 text-sm font-bold text-slate-700 uppercase tracking-wider">Logo Kanan (Header Report)</label>
                            <div class="flex items-center justify-center w-full">
                                <label for="logo-kanan-upload" class="flex flex-col items-center justify-center w-full h-32 border-2 border-slate-300 border-dashed rounded-2xl cursor-pointer bg-slate-50 hover:bg-slate-100 transition relative overflow-hidden group">
                                    <div id="preview-logo-kanan-create" class="absolute inset-0 hidden">
                                        <img id="logo-kanan-preview-img" src="#" alt="Preview" class="w-full h-full object-contain p-2">
                                    </div>
                                    <div id="placeholder-logo-kanan-create" class="flex flex-col items-center justify-center">
                                        <i data-lucide="image" class="w-6 h-6 text-slate-400 mb-1"></i>
                                        <p class="text-[10px] text-slate-500 font-bold uppercase">Unggah Logo</p>
                                    </div>
                                    <input id="logo-kanan-upload" name="logo_kanan" type="file" class="hidden" accept="image/*" onchange="previewLogo(this, 'logo-kanan-preview-img', 'preview-logo-kanan-create', 'placeholder-logo-kanan-create')" />
                                </label>
                            </div>
                        </div>

                        <div class="md:col-span-2">
                            <label class="block mb-2 text-sm font-bold text-slate-700 uppercase tracking-wider">Nama
                                Event</label>
                            <input type="text" name="nama_event" value="{{ old('nama_event') }}"
                                class="bg-slate-50 border border-slate-300 text-slate-900 text-sm rounded-xl focus:ring-2 focus:ring-ksc-blue focus:border-ksc-blue block w-full p-3.5 outline-none font-bold"
                                placeholder="Contoh: KSC Fun Swimming 2026" required>
                        </div>

                        <div>
                            <label
                                class="block mb-2 text-sm font-bold text-slate-700 uppercase tracking-wider">Penyelenggara</label>
                            <select name="uid_author"
                                class="bg-slate-50 border border-slate-300 text-slate-900 text-sm rounded-xl focus:ring-2 focus:ring-ksc-blue block w-full p-3.5 outline-none font-bold">
                                @foreach ($authors as $author)
                                    <option value="{{ $author['uid'] }}"
                                        {{ $user['uid'] === $author['uid'] ? 'selected' : '' }}>
                                        {{ $author['nama_lengkap'] }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block mb-2 text-sm font-bold text-slate-700 uppercase tracking-wider">Lokasi Pertandingan</label>
                            <input type="text" name="lokasi_event" value="{{ old('lokasi_event') }}"
                                class="bg-slate-50 border border-slate-300 text-slate-900 text-sm rounded-xl focus:ring-2 focus:ring-ksc-blue block w-full p-3.5 outline-none font-bold"
                                placeholder="Nama Kolam / Alamat Lengkap" required>
                        </div>

                        <div>
                            <label class="block mb-2 text-sm font-bold text-slate-700 uppercase tracking-wider">Tanggal Mulai</label>
                            <input type="date" name="tanggal_mulai" value="{{ old('tanggal_mulai') }}"
                                class="bg-slate-50 border border-slate-300 text-slate-900 text-sm rounded-xl focus:ring-2 focus:ring-ksc-blue block w-full p-3.5 outline-none font-bold"
                                required>
                        </div>

                        <div>
                            <label class="block mb-2 text-sm font-bold text-slate-700 uppercase tracking-wider">Tanggal Selesai</label>
                            <input type="date" name="tanggal_selesai" value="{{ old('tanggal_selesai') }}"
                                class="bg-slate-50 border border-slate-300 text-slate-900 text-sm rounded-xl focus:ring-2 focus:ring-ksc-blue block w-full p-3.5 outline-none font-bold"
                                required>
                        </div>

                        <div>
                            <label class="block mb-2 text-sm font-bold text-slate-700 uppercase tracking-wider">Waktu Mulai (WIB)</label>
                            <input type="time" name="waktu_mulai" value="{{ old('waktu_mulai', '08:00') }}"
                                class="bg-slate-50 border border-slate-300 text-slate-900 text-sm rounded-xl focus:ring-2 focus:ring-ksc-blue block w-full p-3.5 outline-none font-bold"
                                required>
                        </div>

                        <div>
                            <label class="block mb-2 text-sm font-bold text-slate-700 uppercase tracking-wider">Jumlah Lintasan</label>
                            <input type="number" name="jumlah_lintasan" value="{{ old('jumlah_lintasan', 8) }}"
                                class="bg-slate-50 border border-slate-300 text-slate-900 text-sm rounded-xl focus:ring-2 focus:ring-ksc-blue block w-full p-3.5 outline-none font-bold"
                                required min="1">
                        </div>

                        <div class="md:col-span-2">
                            <label class="block mb-2 text-sm font-bold text-slate-700 uppercase tracking-wider">Status Publikasi</label>
                            <select name="status_event"
                                class="bg-slate-50 border border-slate-300 text-slate-900 text-sm rounded-xl focus:ring-2 focus:ring-ksc-blue block w-full p-3.5 outline-none font-bold">
                                <option value="berjalan">Berjalan (Registration Open)</option>
                                <option value="ditunda">Ditunda (Postponed)</option>
                                <option value="ditutup">Ditutup (Registration Closed)</option>
                            </select>
                        </div>

                    <div class="md:col-span-2 space-y-4">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                            <label
                                class="text-sm font-black text-slate-900 uppercase tracking-widest flex items-center gap-2">
                                <i data-lucide="list-checks" class="w-5 h-5 text-ksc-blue"></i>
                                Daftar Lomba & Biaya
                            </label>
                            <button type="button" @click="addMatch()"
                                class="text-[10px] font-bold bg-blue-50 text-ksc-blue px-3 py-1.5 rounded-lg hover:bg-ksc-blue hover:text-white transition flex items-center gap-1 uppercase tracking-tighter">
                                <i data-lucide="plus" class="w-3 h-3"></i> Tambah Lomba
                            </button>
                        </div>

                        <div class="space-y-6">
                            <template x-for="(match, index) in matches" :key="index">
                                <div
                                    class="bg-slate-50/50 border border-slate-200 p-6 rounded-2xl relative group shadow-sm hover:shadow-md transition-all">
                                    <button type="button" @click="removeMatch(index)" x-show="matches.length > 1"
                                        class="absolute -top-2 -right-2 bg-red-100 text-red-600 p-2 rounded-full shadow-lg hover:bg-red-600 hover:text-white transition z-10 border-2 border-white">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </button>

                                    <div class="grid grid-cols-1 md:grid-cols-12 gap-5 text-left">
                                        <div class="md:col-span-4">
                                            <label
                                                class="block mb-1 text-[10px] font-black text-slate-400 uppercase tracking-widest">Pilih
                                                Gaya / Lomba</label>
                                            <select :name="'matches[' + index + '][uid_category]'"
                                                x-model="match.uid_category" @change="updateMatchName(index)"
                                                class="bg-white border border-slate-200 text-slate-900 text-sm font-bold rounded-xl focus:ring-2 focus:ring-blue-100 block w-full p-3 outline-none"
                                                required>
                                                <option value="" disabled>-- Pilih Gaya --</option>
                                                <template x-for="cat in categories" :key="cat.uid">
                                                    <option :value="cat.uid" x-text="cat.nama_kategori"></option>
                                                </template>
                                            </select>
                                            <input type="hidden" :name="'matches[' + index + '][nama_acara]'"
                                                :value="match.nama_acara">
                                        </div>
                                        <div class="md:col-span-2">
                                            <label
                                                class="block mb-1 text-[10px] font-black text-slate-400 uppercase tracking-widest">Waktu</label>
                                            <input type="time" :name="'matches[' + index + '][waktu_mulai]'"
                                                x-model="match.waktu_mulai"
                                                class="bg-white border border-slate-200 text-slate-900 text-sm font-bold rounded-xl focus:ring-2 focus:ring-blue-100 block w-full p-3 outline-none"
                                                required>
                                        </div>
                                        <div class="md:col-span-2">
                                            <label
                                                class="block mb-1 text-[10px] font-black text-slate-400 uppercase tracking-widest">Tipe</label>
                                            <select :name="'matches[' + index + '][tipe_biaya]'" x-model="match.tipe_biaya"
                                                class="bg-white border border-slate-200 text-slate-900 text-sm font-bold rounded-xl focus:ring-2 focus:ring-blue-100 block w-full p-3 outline-none">
                                                <option value="berbayar">Berbayar</option>
                                                <option value="gratis">Gratis</option>
                                            </select>
                                        </div>
                                        <div class="md:col-span-2">
                                            <label
                                                class="block mb-1 text-[10px] font-black text-slate-400 uppercase tracking-widest">Biaya</label>
                                            <input type="number" :name="'matches[' + index + '][biaya_pendaftaran]'"
                                                x-model="match.biaya_pendaftaran"
                                                class="bg-white border border-slate-200 text-slate-900 text-sm font-bold rounded-xl focus:ring-2 focus:ring-blue-100 block w-full p-3 outline-none"
                                                :disabled="match.tipe_biaya === 'gratis'"
                                                :class="match.tipe_biaya === 'gratis' ? 'opacity-50 bg-slate-100' : ''"
                                                required>
                                        </div>
                                        <div class="md:col-span-2">
                                            <label
                                                class="block mb-1 text-[10px] font-black text-slate-400 uppercase tracking-widest">Jumlah Seri</label>
                                            <input type="number" :name="'matches[' + index + '][jumlah_seri]'"
                                                x-model="match.jumlah_seri"
                                                class="bg-white border border-slate-200 text-slate-900 text-sm font-bold rounded-xl focus:ring-2 focus:ring-blue-100 block w-full p-3 outline-none"
                                                placeholder="1" required min="1">
                                        </div>

                                        <!-- Layout perbaikan Syarat Partisipasi -->
                                        <div class="md:col-span-12 mt-4">
                                            <div class="p-5 bg-white border border-slate-100 rounded-xl shadow-inner">
                                                <div class="flex items-center justify-between mb-4">
                                                    <h4
                                                        class="text-xs font-black text-slate-500 uppercase tracking-widest flex items-center gap-2">
                                                        <i data-lucide="shield-check" class="w-4 h-4 text-blue-500"></i>
                                                        Persyaratan Khusus
                                                        <span
                                                            class="text-[9px] font-medium text-slate-400 lowercase tracking-normal">(Opsional)</span>
                                                    </h4>
                                                    <button type="button" @click="addRequirement(index)"
                                                        class="text-[10px] bg-slate-900 text-white px-4 py-2 rounded-lg hover:bg-black transition font-bold flex items-center gap-2 shadow-md">
                                                        <i data-lucide="plus" class="w-3.5 h-3.5"></i> Tambah Syarat
                                                    </button>
                                                </div>

                                                <div class="space-y-3">
                                                    <template x-for="(req, reqIndex) in match.requirements"
                                                        :key="reqIndex">
                                                        <div
                                                            class="grid grid-cols-1 md:grid-cols-12 gap-3 items-center bg-slate-50 p-3 rounded-lg border border-slate-100">
                                                            <div class="md:col-span-4">
                                                                <label
                                                                    class="block mb-1 text-[9px] font-black text-slate-400 uppercase">Parameter</label>
                                                                <select
                                                                    :name="'matches[' + index + '][requirements][' + reqIndex +
                                                                        '][parameter_name]'"
                                                                    x-model="req.parameter_name"
                                                                    @change="onParamChange(index, reqIndex)"
                                                                    class="w-full text-xs font-bold px-3 py-2 bg-white border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-100 outline-none transition uppercase">
                                                                    <option value="">Pilih Parameter</option>
                                                                    <template x-for="p in master_params" :key="p.uid">
                                                                        <option :value="p.parameter_key" x-text="p.display_name"></option>
                                                                    </template>
                                                                </select>
                                                            </div>
                                                            <div class="md:col-span-2">
                                                                <label
                                                                    class="block mb-1 text-[9px] font-black text-slate-400 uppercase">Operator</label>
                                                                <select
                                                                    :name="'matches[' + index + '][requirements][' + reqIndex +
                                                                        '][operator]'"
                                                                    x-model="req.operator"
                                                                    class="w-full text-xs font-bold px-3 py-2 bg-white border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-100 outline-none transition">
                                                                    <template x-for="op in req.allowed_operators" :key="op">
                                                                        <option :value="op" x-text="op"></option>
                                                                    </template>
                                                                </select>
                                                            </div>
                                                            <div class="md:col-span-5">
                                                                <label
                                                                    class="block mb-1 text-[9px] font-black text-slate-400 uppercase">Nilai
                                                                    Syarat</label>
                                                                <!-- Text / Number / Date -->
                                                                <template x-if="req.input_type !== 'select'">
                                                                    <input :type="req.input_type || 'text'"
                                                                        :name="'matches[' + index + '][requirements][' + reqIndex +
                                                                            '][parameter_value]'"
                                                                        x-model="req.parameter_value"
                                                                        class="w-full text-xs font-bold px-3 py-2 bg-white border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-100 outline-none transition">
                                                                </template>
                                                                <!-- Select Dropdown -->
                                                                <template x-if="req.input_type === 'select'">
                                                                    <select
                                                                        :name="'matches[' + index + '][requirements][' + reqIndex +
                                                                            '][parameter_value]'"
                                                                        x-model="req.parameter_value"
                                                                        class="w-full text-xs font-bold px-3 py-2 bg-white border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-100 outline-none transition">
                                                                        <option value="">Pilih...</option>
                                                                        <template x-for="opt in req.options" :key="opt">
                                                                            <option :value="opt" x-text="opt"></option>
                                                                        </template>
                                                                    </select>
                                                                </template>
                                                            </div>
                                                            <div class="md:col-span-1 flex justify-center pt-4 md:pt-0">
                                                                <button type="button"
                                                                    @click="removeRequirement(index, reqIndex)"
                                                                    class="p-2 text-red-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition">
                                                                    <i data-lucide="x" class="w-4 h-4"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </template>
                                                    <div x-show="match.requirements.length === 0"
                                                        class="text-center py-6 border-2 border-dashed border-slate-100 rounded-xl bg-slate-50/30">
                                                        <p
                                                            class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">
                                                            Tanpa Syarat Khusus</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>


                    <div class="md:col-span-2">
                        <label class="block mb-2 text-sm font-bold text-slate-700 uppercase tracking-wider">Metode
                            Pembayaran (Jika Ada Lomba Berbayar)</label>
                        <select name="uid_payment_method"
                            class="bg-slate-50 border border-slate-300 text-slate-900 text-sm rounded-xl focus:ring-2 focus:ring-ksc-blue block w-full p-3.5 outline-none font-bold">
                            <option value="" disabled selected>-- Pilih Rekening Pembayaran --</option>
                            @foreach ($payment_methods as $pm)
                                <option value="{{ $pm['uid'] }}">{{ $pm['bank'] }} - {{ $pm['rekening'] }}
                                    (a.n {{ $pm['atas_nama'] }})
                                </option>
                            @endforeach
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
    @endif

    @if ($user->can('manage-events'))
    @foreach ($events['data'] as $event)
        <div id="modal-edit-event-{{ $event['uid'] }}" tabindex="-1" aria-hidden="true"
            class="hidden fixed top-0 left-0 right-0 z-[70] w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-full max-h-full items-center justify-center bg-slate-900/60 backdrop-blur-sm transition-opacity">
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

                    <form id="form-edit-event-{{ $event['uid'] }}" class="p-6 md:p-8 overflow-y-auto max-h-[75vh]"
                        action="{{ url('/' . $user['nama_role'] . '/' . $user['uid'] . '/' . $event['uid'] . '/dashboard/management-event/edit/process') }}"
                        method="POST" enctype="multipart/form-data" x-data="{
                            tipe: 'berbayar',
                            categories: {{ json_encode($categories) }},
                            master_params: {{ json_encode($requirement_parameters) }},
                            matches: {{ json_encode($event['matches_data']) }},
                            addMatch() { this.matches.push({ uid_category: '', nomor_acara: '', nama_acara: '', tipe_biaya: 'berbayar', biaya_pendaftaran: 0, jumlah_seri: 1, waktu_mulai: '08:00', requirements: [] }); },
                            updateMatchName(index) {
                                const cat = this.categories.find(c => c.uid === this.matches[index].uid_category);
                                if (cat) { this.matches[index].nama_acara = cat.nama_kategori; }
                            },
                            removeMatch(index) { this.matches.splice(index, 1); },
                            addRequirement(matchIndex) {
                                this.matches[matchIndex].requirements.push({ parameter_name: '', operator: '=', parameter_value: '', input_type: 'text', options: [], allowed_operators: [] });
                            },
                            removeRequirement(matchIndex, reqIndex) { this.matches[matchIndex].requirements.splice(reqIndex, 1); },
                            onParamChange(mIdx, rIdx) {
                                const pName = this.matches[mIdx].requirements[rIdx].parameter_name;
                                const param = this.master_params.find(p => p.parameter_key === pName);
                                if (param) {
                                    this.matches[mIdx].requirements[rIdx].input_type = param.input_type || 'text';
                                    this.matches[mIdx].requirements[rIdx].options = JSON.parse(param.input_options || '[]');
                                    this.matches[mIdx].requirements[rIdx].allowed_operators = JSON.parse(param.allowed_operators || '[]');
                                    if (!this.matches[mIdx].requirements[rIdx].allowed_operators.includes(this.matches[mIdx].requirements[rIdx].operator)) {
                                        this.matches[mIdx].requirements[rIdx].operator = this.matches[mIdx].requirements[rIdx].allowed_operators[0] || '=';
                                    }
                                }
                            }
                        }">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-left">

                            <div class="md:col-span-2">
                                <label class="block mb-2 text-sm font-bold text-slate-700 uppercase tracking-wider text-left">Banner Event (Klik untuk Ubah)</label>
                                <div class="flex items-center justify-center w-full">
                                    <label for="edit-banner-upload-{{ $event['uid'] }}"
                                        class="flex flex-col items-center justify-center w-full h-52 border-2 border-slate-300 border-dashed rounded-2xl cursor-pointer bg-slate-50 hover:bg-slate-100 transition relative overflow-hidden group">
                                        <div id="edit-preview-container-{{ $event['uid'] }}" class="absolute inset-0">
                                            <img id="edit-banner-preview-{{ $event['uid'] }}"
                                                src="{{ $event['banner_event'] == null ? url('/file/dummy/dummy.webp') : url('/file/banner-event/' . $event['banner_event']) }}"
                                                alt="Preview" class="w-full h-full object-cover">
                                            <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 flex items-center justify-center transition">
                                                <p class="text-white text-xs font-bold uppercase tracking-widest">Ganti Gambar</p>
                                            </div>
                                        </div>
                                        <input id="edit-banner-upload-{{ $event['uid'] }}" name="banner_event" type="file" class="hidden" accept="image/*" onchange="previewEditBanner(this, '{{ $event['uid'] }}')" />
                                    </label>
                                </div>
                            </div>

                            <div>
                                <label class="block mb-2 text-sm font-bold text-slate-700 uppercase tracking-wider text-left">Logo Kiri (Header Report)</label>
                                <div class="flex items-center justify-center w-full">
                                    <label for="edit-logo-kiri-upload-{{ $event['uid'] }}" class="flex flex-col items-center justify-center w-full h-32 border-2 border-slate-300 border-dashed rounded-2xl cursor-pointer bg-slate-50 hover:bg-slate-100 transition relative overflow-hidden group">
                                        <div id="edit-preview-logo-kiri-{{ $event['uid'] }}" class="absolute inset-0 {{ $event['logo_kiri'] ? '' : 'hidden' }}">
                                            <img id="edit-logo-kiri-preview-img-{{ $event['uid'] }}" src="{{ $event['logo_kiri'] ? url('/file/logos/' . $event['logo_kiri']) : '#' }}" alt="Preview" class="w-full h-full object-contain p-2">
                                        </div>
                                        <div id="edit-placeholder-logo-kiri-{{ $event['uid'] }}" class="flex flex-col items-center justify-center {{ $event['logo_kiri'] ? 'hidden' : '' }}">
                                            <i data-lucide="image" class="w-6 h-6 text-slate-400 mb-1"></i>
                                            <p class="text-[10px] text-slate-500 font-bold uppercase">Unggah Logo</p>
                                        </div>
                                        <input id="edit-logo-kiri-upload-{{ $event['uid'] }}" name="logo_kiri" type="file" class="hidden" accept="image/*" onchange="previewLogo(this, 'edit-logo-kiri-preview-img-{{ $event['uid'] }}', 'edit-preview-logo-kiri-{{ $event['uid'] }}', 'edit-placeholder-logo-kiri-{{ $event['uid'] }}')" />
                                    </label>
                                </div>
                            </div>

                            <div>
                                <label class="block mb-2 text-sm font-bold text-slate-700 uppercase tracking-wider text-left">Logo Kanan (Header Report)</label>
                                <div class="flex items-center justify-center w-full">
                                    <label for="edit-logo-kanan-upload-{{ $event['uid'] }}" class="flex flex-col items-center justify-center w-full h-32 border-2 border-slate-300 border-dashed rounded-2xl cursor-pointer bg-slate-50 hover:bg-slate-100 transition relative overflow-hidden group">
                                        <div id="edit-preview-logo-kanan-{{ $event['uid'] }}" class="absolute inset-0 {{ $event['logo_kanan'] ? '' : 'hidden' }}">
                                            <img id="edit-logo-kanan-preview-img-{{ $event['uid'] }}" src="{{ $event['logo_kanan'] ? url('/file/logos/' . $event['logo_kanan']) : '#' }}" alt="Preview" class="w-full h-full object-contain p-2">
                                        </div>
                                        <div id="edit-placeholder-logo-kanan-{{ $event['uid'] }}" class="flex flex-col items-center justify-center {{ $event['logo_kanan'] ? 'hidden' : '' }}">
                                            <i data-lucide="image" class="w-6 h-6 text-slate-400 mb-1"></i>
                                            <p class="text-[10px] text-slate-500 font-bold uppercase">Unggah Logo</p>
                                        </div>
                                        <input id="edit-logo-kanan-upload-{{ $event['uid'] }}" name="logo_kanan" type="file" class="hidden" accept="image/*" onchange="previewLogo(this, 'edit-logo-kanan-preview-img-{{ $event['uid'] }}', 'edit-preview-logo-kanan-{{ $event['uid'] }}', 'edit-placeholder-logo-kanan-{{ $event['uid'] }}')" />
                                    </label>
                                </div>
                            </div>
                            <div class="md:col-span-2">
                                <label
                                    class="block mb-2 text-sm font-bold text-slate-700 uppercase tracking-wider text-left">Nama
                                    Event</label>
                                <input type="text" name="nama_event" value="{{ $event['nama_event'] }}"
                                    class="bg-slate-50 border border-slate-300 text-slate-900 text-sm rounded-xl focus:ring-2 focus:ring-ksc-blue block w-full p-3.5 outline-none font-bold"
                                    required>
                            </div>

                            <div>
                                <label
                                    class="block mb-2 text-sm font-bold text-slate-700 uppercase tracking-wider text-left">Penyelenggara</label>
                                <select name="uid_author"
                                    class="bg-slate-50 border border-slate-300 text-slate-900 text-sm rounded-xl focus:ring-2 focus:ring-ksc-blue block w-full p-3.5 outline-none font-bold">
                                    @foreach ($authors as $author)
                                        <option value="{{ $author['uid'] }}"
                                            {{ $event['uid_author'] == $author['uid'] ? 'selected' : '' }}>
                                            {{ $author['nama_lengkap'] }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label
                                    class="block mb-2 text-sm font-bold text-slate-700 uppercase tracking-wider text-left">Lokasi
                                    Pertandingan</label>
                                <input type="text" name="lokasi_event" value="{{ $event['lokasi_event'] }}"
                                    class="bg-slate-50 border border-slate-300 text-slate-900 text-sm rounded-xl focus:ring-2 focus:ring-ksc-blue block w-full p-3.5 outline-none font-bold"
                                    required>
                            </div>

                            <div>
                                <label
                                    class="block mb-2 text-sm font-bold text-slate-700 uppercase tracking-wider text-left">Tanggal
                                    Mulai</label>
                                <input type="date" name="tanggal_mulai" value="{{ $event['tanggal_mulai'] }}"
                                    class="bg-slate-50 border border-slate-300 text-slate-900 text-sm rounded-xl block w-full p-3.5 outline-none focus:ring-2 focus:ring-blue-500 font-bold"
                                    required>
                            </div>

                            <div>
                                <label
                                    class="block mb-2 text-sm font-bold text-slate-700 uppercase tracking-wider text-left">Tanggal
                                    Selesai</label>
                                <input type="date" name="tanggal_selesai" value="{{ $event['tanggal_selesai'] }}"
                                    class="bg-slate-50 border border-slate-300 text-slate-900 text-sm rounded-xl block w-full p-3.5 outline-none focus:ring-2 focus:ring-blue-500 font-bold"
                                    required>
                            </div>

                            <div>
                                <label
                                    class="block mb-2 text-sm font-bold text-slate-700 uppercase tracking-wider text-left">Waktu Mulai (WIB)</label>
                                <input type="time" name="waktu_mulai" value="{{ $event['waktu_mulai'] }}"
                                    class="bg-slate-50 border border-slate-300 text-slate-900 text-sm rounded-xl block w-full p-3.5 outline-none focus:ring-2 focus:ring-blue-500 font-bold"
                                    required>
                            </div>

                             <div>
                                <label
                                    class="block mb-2 text-sm font-bold text-slate-700 uppercase tracking-wider text-left">Jumlah Lintasan</label>
                                <input type="number" name="jumlah_lintasan" value="{{ $event['jumlah_lintasan'] }}"
                                    class="bg-slate-50 border border-slate-300 text-slate-900 text-sm rounded-xl focus:ring-2 focus:ring-ksc-blue block w-full p-3.5 outline-none font-bold"
                                    required min="1">
                            </div>

                            <div class="md:col-span-2">
                                <label
                                    class="block mb-2 text-sm font-bold text-slate-700 uppercase tracking-wider text-left">Status
                                    Publikasi</label>
                                <select name="status_event"
                                    class="bg-slate-50 border border-slate-300 text-slate-900 text-sm rounded-xl focus:ring-2 focus:ring-ksc-blue block w-full p-3.5 outline-none font-bold">
                                    <option value="berjalan" {{ $event['status_event'] == 'berjalan' ? 'selected' : '' }}>
                                        Berjalan</option>
                                    <option value="ditunda" {{ $event['status_event'] == 'ditunda' ? 'selected' : '' }}>
                                        Ditunda</option>
                                    <option value="ditutup" {{ $event['status_event'] == 'ditutup' ? 'selected' : '' }}>
                                        Ditutup</option>
                                </select>
                            </div>

                            <!-- List Matches Section -->
                            <div class="md:col-span-2 space-y-4 text-left">
                                <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                                    <label
                                        class="text-xs font-black text-slate-900 uppercase tracking-widest flex items-center gap-2">
                                        <i data-lucide="list-checks" class="w-5 h-5 text-ksc-blue"></i> Daftar Lomba &
                                        Biaya
                                    </label>
                                    <button type="button" @click="addMatch()"
                                        class="text-[10px] font-bold bg-blue-50 text-ksc-blue px-3 py-1.5 rounded-lg hover:bg-ksc-blue hover:text-white transition flex items-center gap-1 uppercase">
                                        <i data-lucide="plus" class="w-3 h-3"></i> Tambah Lomba
                                    </button>
                                </div>

                                <div class="space-y-6 text-left">
                                    <template x-for="(match, mIndex) in matches" :key="mIndex">
                                        <div
                                            class="bg-slate-50/50 border border-slate-200 p-6 rounded-2xl relative group shadow-sm text-left">
                                            <button type="button" @click="removeMatch(mIndex)"
                                                x-show="matches.length > 1"
                                                class="absolute -top-2 -right-2 bg-red-100 text-red-600 p-2 rounded-full shadow-lg border-2 border-white hover:bg-red-600 hover:text-white transition z-10 text-center">
                                                <i data-lucide="trash-2" class="w-4 h-4 text-center"></i>
                                            </button>

                                            <div
                                                class="grid grid-cols-1 md:grid-cols-12 gap-5 text-left border-none shadow-none">
                                                <div class="md:col-span-4 text-left">
                                                    <label
                                                        class="block mb-1 text-[10px] font-black text-slate-400 uppercase tracking-widest text-left">Pilih
                                                        Gaya / Lomba</label>
                                                    <select :name="'matches[' + mIndex + '][uid_category]'"
                                                        x-model="match.uid_category" @change="updateMatchName(mIndex)"
                                                        class="bg-white border border-slate-200 text-slate-900 text-sm font-bold rounded-xl focus:ring-2 focus:ring-blue-100 block w-full p-3 outline-none shadow-none"
                                                        required>
                                                        <option value="" disabled>-- Pilih Gaya --</option>
                                                        <template x-for="cat in categories" :key="cat.uid">
                                                            <option :value="cat.uid" x-text="cat.nama_kategori"
                                                                :selected="cat.uid === match.uid_category"></option>
                                                        </template>
                                                    </select>
                                                    <input type="hidden" :name="'matches[' + mIndex + '][nama_acara]'"
                                                        :value="match.nama_acara">
                                                </div>
                                                <div class="md:col-span-2 text-left">
                                                    <label
                                                        class="block mb-1 text-[10px] font-black text-slate-400 uppercase tracking-widest text-left">Waktu</label>
                                                    <input type="time" :name="'matches[' + mIndex + '][waktu_mulai]'"
                                                        x-model="match.waktu_mulai"
                                                        class="bg-white border border-slate-200 text-slate-900 text-sm font-bold rounded-xl focus:ring-2 focus:ring-blue-100 block w-full p-3 outline-none shadow-none"
                                                        required>
                                                </div>
                                                <div class="md:col-span-2 text-left">
                                                    <label
                                                        class="block mb-1 text-[10px] font-black text-slate-400 uppercase tracking-widest text-left">Tipe</label>
                                                    <select :name="'matches[' + mIndex + '][tipe_biaya]'"
                                                        x-model="match.tipe_biaya"
                                                        class="bg-white border border-slate-200 text-slate-900 text-sm font-bold rounded-xl focus:ring-2 focus:ring-blue-100 block w-full p-3 outline-none">
                                                        <option value="berbayar">Berbayar</option>
                                                        <option value="gratis">Gratis</option>
                                                    </select>
                                                </div>
                                                <div class="md:col-span-2 text-left">
                                                    <label
                                                        class="block mb-1 text-[10px] font-black text-slate-400 uppercase tracking-widest text-left">Biaya</label>
                                                    <input type="number"
                                                        :name="'matches[' + mIndex + '][biaya_pendaftaran]'"
                                                        x-model="match.biaya_pendaftaran"
                                                        class="bg-white border border-slate-200 text-slate-900 text-sm font-bold rounded-xl focus:ring-2 focus:ring-blue-100 block w-full p-3 outline-none shadow-none"
                                                        :disabled="match.tipe_biaya === 'gratis'"
                                                        :class="match.tipe_biaya === 'gratis' ? 'opacity-50 bg-slate-100' :
                                                            ''"
                                                        required>
                                                </div>
                                                <div class="md:col-span-2 text-left">
                                                    <label
                                                        class="block mb-1 text-[10px] font-black text-slate-400 uppercase tracking-widest text-left">Jumlah Seri</label>
                                                    <input type="number" :name="'matches[' + mIndex + '][jumlah_seri]'"
                                                        x-model="match.jumlah_seri"
                                                        class="bg-white border border-slate-200 text-slate-900 text-sm font-bold rounded-xl focus:ring-2 focus:ring-blue-100 block w-full p-3 outline-none shadow-none"
                                                        placeholder="1" required min="1">
                                                </div>

                                                <div class="md:col-span-12 mt-4 text-left border-none shadow-none">
                                                    <div
                                                        class="p-5 bg-white border border-slate-100 rounded-xl shadow-inner border-none">
                                                        <div class="flex items-center justify-between mb-4">
                                                            <h4
                                                                class="text-xs font-black text-slate-500 uppercase tracking-widest flex items-center gap-2">
                                                                <i data-lucide="shield-check"
                                                                    class="w-4 h-4 text-blue-500"></i> Syarat Partisipasi
                                                            </h4>
                                                            <button type="button" @click="addRequirement(mIndex)"
                                                                class="text-[10px] bg-slate-900 text-white px-4 py-2 rounded-lg hover:bg-black transition font-bold flex items-center gap-2">
                                                                <i data-lucide="plus" class="w-3.5 h-3.5"></i> Tambah
                                                            </button>
                                                        </div>

                                                        <div class="space-y-3 text-left border-none shadow-none">
                                                            <template x-for="(req, reqIndex) in match.requirements"
                                                                :key="reqIndex">
                                                                <div
                                                                    class="grid grid-cols-1 md:grid-cols-12 gap-3 items-center bg-slate-50 p-3 rounded-lg border border-slate-100 text-left border-none shadow-none">
                                                                    <div class="md:col-span-4 text-left">
                                                                        <select
                                                                            :name="'matches[' + mIndex + '][requirements][' +
                                                                                reqIndex + '][parameter_name]'"
                                                                            x-model="req.parameter_name"
                                                                            @change="onParamChange(mIndex, reqIndex)"
                                                                            class="w-full text-xs font-bold px-3 py-2 bg-white border border-slate-200 rounded-lg shadow-none uppercase">
                                                                            <option value="">Parameter</option>
                                                                            <template x-for="p in master_params" :key="p.uid">
                                                                                <option :value="p.parameter_key" x-text="p.display_name"></option>
                                                                            </template>
                                                                        </select>
                                                                    </div>
                                                                    <div class="md:col-span-2 text-left">
                                                                        <select
                                                                            :name="'matches[' + mIndex + '][requirements][' +
                                                                                reqIndex + '][operator]'"
                                                                            x-model="req.operator"
                                                                            class="w-full text-xs font-bold px-3 py-2 bg-white border border-slate-200 rounded-lg">
                                                                            <template x-for="op in req.allowed_operators" :key="op">
                                                                                <option :value="op" x-text="op"></option>
                                                                            </template>
                                                                        </select>
                                                                    </div>
                                                                    <div
                                                                        class="md:col-span-5 text-left border-none shadow-none">
                                                                        <template x-if="req.input_type !== 'select'">
                                                                            <input :type="req.input_type || 'text'"
                                                                                :name="'matches[' + mIndex + '][requirements][' +
                                                                                    reqIndex + '][parameter_value]'"
                                                                                x-model="req.parameter_value"
                                                                                class="w-full text-xs font-bold px-3 py-2 bg-white border border-slate-200 rounded-lg">
                                                                        </template>
                                                                        <template x-if="req.input_type === 'select'">
                                                                            <select
                                                                                :name="'matches[' + mIndex + '][requirements][' +
                                                                                    reqIndex + '][parameter_value]'"
                                                                                x-model="req.parameter_value"
                                                                                class="w-full text-xs font-bold px-3 py-2 bg-white border border-slate-200 rounded-lg">
                                                                                <option value="">Pilih...</option>
                                                                                <template x-for="opt in req.options" :key="opt">
                                                                                    <option :value="opt" x-text="opt"></option>
                                                                                </template>
                                                                            </select>
                                                                        </template>
                                                                    </div>
                                                                    <div
                                                                        class="md:col-span-1 flex justify-center text-left pt-2 md:pt-0">
                                                                        <button type="button"
                                                                            @click="removeRequirement(mIndex, reqIndex)"
                                                                            class="p-2 text-red-400 hover:text-red-600 transition"><i
                                                                                data-lucide="x"
                                                                                class="w-4 h-4"></i></button>
                                                                    </div>
                                                                </div>
                                                            </template>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>

                            <div class="md:col-span-2">
                                <label class="block mb-2 text-sm font-bold text-slate-700 uppercase tracking-wider text-left">Metode Pembayaran</label>
                                <select name="uid_payment_method"
                                    class="bg-slate-50 border border-slate-200 text-slate-900 text-sm rounded-xl focus:ring-2 focus:ring-ksc-blue block w-full p-3.5 outline-none font-bold shadow-none">
                                    <option value="" disabled selected>-- Pilih Rekening Pembayaran --</option>
                                    @foreach ($payment_methods as $pm)
                                        <option value="{{ $pm['uid'] }}" {{ $event['uid_payment_method'] == $pm['uid'] ? 'selected' : '' }}>
                                            {{ $pm['bank'] }} - {{ $pm['rekening'] }} (a.n {{ $pm['atas_nama'] }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="md:col-span-2">
                                <label class="block mb-2 text-sm font-bold text-slate-700 uppercase tracking-wider text-left">Deskripsi & Peraturan</label>
                                <div class="bg-slate-50 border border-slate-200 rounded-2xl overflow-hidden shadow-none font-sans">
                                    <div id="edit-editor-{{ $event['uid'] }}" class="h-56 bg-white text-left">{!! $event['deskripsi'] !!}</div>
                                    <input type="hidden" name="deskripsi" id="edit-deskripsi-input-{{ $event['uid'] }}" value="{{ $event['deskripsi'] }}">
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center pt-8 mt-6 border-t border-slate-100 space-x-3 justify-end">
                            <button data-modal-hide="modal-edit-event-{{ $event['uid'] }}" type="button"
                                class="text-slate-500 bg-white hover:bg-slate-50 rounded-xl border border-slate-200 text-sm font-bold px-8 py-3 transition">Batal</button>
                            <button type="submit"
                                class="text-white bg-slate-900 hover:bg-black font-bold rounded-xl text-sm px-10 py-3 shadow-xl transition-all">Simpan Perubahan</button>
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
    @endif

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

            @if ($user->can('manage-events'))
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
            @endif
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

        function previewLogo(input, imgId, containerId, placeholderId) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById(imgId).setAttribute('src', e.target.result);
                    document.getElementById(containerId).classList.remove('hidden');
                    document.getElementById(placeholderId).classList.add('hidden');
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
@endsection
