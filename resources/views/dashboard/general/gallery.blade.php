@extends('layouts.layout-dashboard.app')

@section('dashboard-section')
    <div class="p-4 md:p-8 overflow-y-auto">
        {{-- HEADER --}}
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8">
            <div>
                <h2 class="text-2xl font-bold text-slate-900 leading-tight">Media Gallery</h2>
                <p class="text-sm text-slate-500">Kelola foto dokumentasi event KSC</p>
            </div>

            {{-- Storage Usage Card --}}
            <div class="w-full md:w-80 bg-white border border-slate-200 rounded-2xl p-4 shadow-sm">
                <div class="flex justify-between items-center mb-2">
                    <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Storage Usage</span>
                    <span class="text-[10px] font-bold text-slate-900">{{ $stats['item_percentage'] }}%</span>
                </div>
                <div class="w-full h-2 bg-slate-100 rounded-full overflow-hidden mb-2">
                    <div class="h-full bg-blue-500 rounded-full transition-all duration-1000"
                        style="width: {{ $stats['item_percentage'] }}%"></div>
                </div>
                <div class="flex justify-between items-center">
                    <p class="text-[8px] font-bold text-slate-400 uppercase">
                        {{ $stats['total_items'] }} / {{ $stats['max_items'] }} <span class="ml-1">Photos</span>
                    </p>
                    <p class="text-[8px] font-bold text-slate-900 uppercase tracking-tighter">
                        {{ $stats['total_size_mb'] }} MB <span class="text-slate-300 mx-0.5">/</span> 2 GB
                    </p>
                </div>
            </div>

            <button data-modal-target="modal-tambah-foto" data-modal-toggle="modal-tambah-foto"
                class="flex items-center gap-2 bg-slate-900 hover:bg-black text-white px-5 py-3 rounded-xl font-bold text-xs transition shadow-lg active:scale-95"
                type="button">
                <i data-lucide="plus" class="w-4 h-4"></i>
                <span>Upload Media</span>
            </button>
        </div>

        {{-- MASONRY GRID --}}
        <div class="columns-2 md:columns-4 lg:columns-6 xl:columns-8 gap-2 space-y-2">
            @foreach ($galleries['data'] as $g)
                <div
                    class="relative group break-inside-avoid rounded-xl overflow-hidden border border-slate-200 bg-white shadow-sm hover:shadow-md transition-shadow">
                    <img src="{{ url('/file/gallery/' . $g['foto_event']) }}" class="w-full h-auto object-cover"
                        loading="lazy">
                    <div
                        class="absolute inset-0 bg-slate-900/60 opacity-0 group-hover:opacity-100 transition-all flex items-center justify-center gap-1.5">
                        <div class="absolute top-2 left-2 right-2">
                            <span
                                class="px-2 py-1 bg-white/90 backdrop-blur-sm text-[8px] font-black text-slate-900 rounded-md uppercase tracking-wider line-clamp-1">
                                {{ $g->event->nama_event ?? 'N/A' }}
                            </span>
                        </div>

                        {{-- Tombol Edit --}}
                        <button type="button"
                            onclick="openEditModal('{{ $g['uid'] }}', '{{ $g['uid_event'] }}', '{{ url('/file/gallery/' . $g['foto_event']) }}')"
                            class="p-2 bg-white/20 hover:bg-white text-white hover:text-slate-900 rounded-lg transition-all transform hover:scale-110">
                            <i data-lucide="edit-3" class="w-4 h-4"></i>
                        </button>

                        {{-- Tombol Hapus --}}
                        <button type="button" onclick="openDeleteModal('{{ $g['uid'] }}')"
                            class="p-2 bg-red-500/80 hover:bg-red-600 text-white rounded-lg transition-all transform hover:scale-110">
                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                        </button>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Custom Pagination --}}
        <div class="mt-6 flex flex-col md:flex-row items-center justify-between gap-4 px-2 pb-10">
            <div class="text-left">
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest">
                    Halaman <span class="text-slate-900">{{ $galleries['current_page'] }}</span>
                    dari <span class="text-slate-900">{{ $galleries['last_page'] }}</span>
                    — Total <span class="text-slate-900">{{ $galleries['total'] }}</span> Media
                </p>
            </div>

            <div class="flex items-center gap-2">
                @if ($galleries['current_page'] > 1)
                    <a href="{{ url('/' . $user['nama_role'] . '/dashboard/management-gallery/page/' . ($galleries['current_page'] - 1)) }}"
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
                    <span class="text-xs font-black text-slate-900">{{ $galleries['current_page'] }}</span>
                </div>

                @if ($galleries['current_page'] < $galleries['last_page'])
                    <a href="{{ url('/' . $user['nama_role'] . '/dashboard/management-gallery/page/' . ($galleries['current_page'] + 1)) }}"
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

    {{-- MODAL UPLOAD --}}
    <div id="modal-tambah-foto" tabindex="-1" aria-hidden="true"
        class="fixed top-0 left-0 right-0 z-[60] hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-full max-h-full bg-slate-900/50 backdrop-blur-sm">
        <div class="relative w-full max-w-md max-h-full mx-auto mt-20 md:mt-0">
            <div class="relative bg-white rounded-3xl shadow-2xl overflow-hidden">
                <div class="p-6 border-b border-slate-50 flex justify-between items-center bg-white">
                    <h3 class="text-sm font-black text-slate-900 uppercase tracking-widest">Upload Media</h3>
                    <button data-modal-hide="modal-tambah-foto" type="button" class="text-slate-400 hover:text-slate-900 transition-colors">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>
                <form class="p-6 space-y-4" method="POST" action="{{ url('/' . $user['nama_role'] . '/' . $user['uid'] . '/dashboard/management-gallery/create/process') }}" enctype="multipart/form-data">
                    @csrf
                    <div>
                        <label class="block mb-1.5 text-[9px] font-black text-slate-400 uppercase tracking-widest">Pilih Event</label>
                        <select name="uid_event" required class="w-full bg-slate-50 border border-slate-200 text-xs font-bold rounded-xl p-3 outline-none focus:ring-2 focus:ring-slate-200">
                            <option value="" disabled selected>-- Pilih Event --</option>
                            @foreach ($events as $event)
                                <option value="{{ $event['uid'] }}">{{ $event['nama_event'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block mb-1.5 text-[9px] font-black text-slate-400 uppercase tracking-widest">Media File</label>
                        <div class="relative group">
                            <input name="foto_event[]" type="file" id="imageInput" accept="image/*" required multiple class="absolute inset-0 w-full h-full opacity-0 z-50 cursor-pointer">
                            <div id="placeholderContainer" class="w-full h-48 border-2 border-dashed border-slate-100 rounded-3xl flex flex-col items-center justify-center bg-slate-50/50 group-hover:bg-blue-50 transition-all overflow-hidden">
                                <div class="w-12 h-12 bg-white rounded-2xl shadow-sm flex items-center justify-center mb-3">
                                    <i data-lucide="upload-cloud" class="w-6 h-6 text-blue-500"></i>
                                </div>
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Klik / Seret Foto</p>
                            </div>
                            <div id="previewContainer" class="hidden relative w-full h-64 bg-slate-50 border border-slate-100 rounded-3xl overflow-hidden">
                                <div class="absolute top-0 left-0 right-0 p-3 bg-white/80 backdrop-blur-md z-10 border-b border-slate-50 flex justify-between items-center">
                                    <span id="previewCount" class="text-[8px] font-black text-slate-900 uppercase tracking-widest">0 Foto</span>
                                    <span class="text-[8px] font-bold text-slate-400 uppercase cursor-pointer hover:text-red-500" onclick="resetInput()">Reset</span>
                                </div>
                                <div id="previewGrid" class="grid grid-cols-3 gap-2 p-3 mt-10 overflow-y-auto max-h-[calc(100%-2.5rem)] scrollbar-hide"></div>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="w-full py-4 bg-slate-900 text-white font-black text-[10px] uppercase tracking-widest rounded-xl shadow-lg hover:bg-black transition-all">
                        Simpan ke Gallery
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- MODAL EDIT MEDIA --}}
    <div id="modal-edit-foto" tabindex="-1" aria-hidden="true"
        class="fixed top-0 left-0 right-0 z-[60] hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-full max-h-full bg-slate-900/50 backdrop-blur-sm">
        <div class="relative w-full max-w-md max-h-full mx-auto mt-20 md:mt-0">
            <div class="relative bg-white rounded-3xl shadow-2xl overflow-hidden">
                <div class="p-6 border-b border-slate-50 flex justify-between items-center">
                    <h3 class="text-sm font-black text-slate-900 uppercase tracking-widest">Edit Media</h3>
                    <button type="button" onclick="closeEditModal()" class="text-slate-400 hover:text-slate-900 transition-colors">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>
                <form id="formEdit" class="p-6 space-y-4" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div>
                        <label class="block mb-1.5 text-[9px] font-black text-slate-400 uppercase tracking-widest">Pindah Ke Event</label>
                        <select name="uid_event" id="edit_uid_event" required class="w-full bg-slate-50 border border-slate-200 text-xs font-bold rounded-xl p-3 outline-none">
                            @foreach ($events as $event)
                                <option value="{{ $event['uid'] }}">{{ $event['nama_event'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block mb-1.5 text-[9px] font-black text-slate-400 uppercase tracking-widest">Update Foto</label>
                        <div class="relative group">
                            <input name="foto_event" type="file" id="imageInputEdit" accept="image/*" class="absolute inset-0 w-full h-full opacity-0 z-50 cursor-pointer">
                            <div id="previewContainerEdit" class="relative w-full h-48 rounded-2xl overflow-hidden border border-slate-200">
                                <img id="imagePreviewEdit" src="" class="w-full h-full object-cover">
                                <div class="absolute inset-0 bg-slate-900/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                    <p class="text-[10px] text-white font-bold uppercase tracking-widest">Ganti Foto</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="flex gap-2">
                        <button type="button" onclick="closeEditModal()" class="flex-1 py-4 bg-slate-100 text-slate-500 font-black text-[10px] uppercase tracking-widest rounded-xl hover:bg-slate-200">Batal</button>
                        <button type="submit" class="flex-[2] py-4 bg-blue-600 text-white font-black text-[10px] uppercase tracking-widest rounded-xl shadow-lg hover:bg-blue-700">Update Gallery</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- MODAL HAPUS FOTO --}}
    <div id="modal-hapus-foto" tabindex="-1" aria-hidden="true"
        class="fixed top-0 left-0 right-0 z-[70] hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-full max-h-full bg-slate-900/60 backdrop-blur-md">
        <div class="relative w-full max-w-sm max-h-full mx-auto mt-32 md:mt-0">
            <div class="relative bg-white rounded-[2.5rem] shadow-2xl p-8 text-center">
                <div class="w-20 h-20 bg-red-50 text-red-500 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i data-lucide="trash-2" class="w-10 h-10"></i>
                </div>
                <h3 class="text-xl font-black text-slate-900 mb-2 uppercase tracking-tight">Hapus Foto?</h3>
                <p class="text-sm text-slate-500 font-medium mb-8">Tindakan ini tidak dapat dibatalkan.</p>
                <div class="flex flex-col gap-3">
                    <form id="formDelete" method="POST">
                        @csrf
                        <button type="submit" class="w-full py-4 bg-red-500 hover:bg-red-600 text-white font-black text-[10px] uppercase tracking-widest rounded-2xl shadow-xl active:scale-95 transition-all">
                            Ya, Hapus Permanen
                        </button>
                    </form>
                    <button type="button" onclick="closeDeleteModal()"
                        class="w-full py-4 bg-slate-100 text-slate-500 font-black text-[10px] uppercase tracking-widest rounded-2xl hover:bg-slate-200 transition-all">
                        Batalkan
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Store instances globally
        let editModalInstance;
        let deleteModalInstance;

        function openEditModal(uid, eventUid, imageSrc) {
            const form = document.getElementById('formEdit');
            const select = document.getElementById('edit_uid_event');
            const preview = document.getElementById('imagePreviewEdit');
            
            form.action = `{{ url('/' . $user['nama_role'] . '/' . $user['uid']) }}/dashboard/management-gallery/${uid}/edit/process`;
            select.value = eventUid;
            preview.src = imageSrc;

            editModalInstance = new Modal(document.getElementById('modal-edit-foto'));
            editModalInstance.show();
        }

        function closeEditModal() {
            if (editModalInstance) editModalInstance.hide();
        }

        function openDeleteModal(uid) {
            const form = document.getElementById('formDelete');
            form.action = `{{ url('/' . $user['nama_role'] . '/' . $user['uid']) }}/dashboard/management-gallery/${uid}/delete/process`;

            deleteModalInstance = new Modal(document.getElementById('modal-hapus-foto'));
            deleteModalInstance.show();
        }

        function closeDeleteModal() {
            if (deleteModalInstance) deleteModalInstance.hide();
        }

        function resetInput() {
            const imageInput = document.getElementById('imageInput');
            const previewGrid = document.getElementById('previewGrid');
            const previewContainer = document.getElementById('previewContainer');
            const placeholderContainer = document.getElementById('placeholderContainer');

            imageInput.value = '';
            previewGrid.innerHTML = '';
            previewContainer.classList.add('hidden');
            placeholderContainer.classList.remove('hidden');
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Preview Logic
            const imageInput = document.getElementById('imageInput');
            const previewGrid = document.getElementById('previewGrid');
            const previewContainer = document.getElementById('previewContainer');
            const placeholderContainer = document.getElementById('placeholderContainer');
            const previewCount = document.getElementById('previewCount');

            if (imageInput) {
                imageInput.addEventListener('change', function() {
                    const files = Array.from(this.files);
                    if (files.length > 0) {
                        if (files.length > 10) {
                            alert('Maksimal 10 foto sekaligus.');
                            this.value = '';
                            return;
                        }
                        previewGrid.innerHTML = '';
                        previewCount.innerText = `${files.length} Foto Terpilih`;
                        previewContainer.classList.remove('hidden');
                        placeholderContainer.classList.add('hidden');

                        files.forEach(file => {
                            const reader = new FileReader();
                            reader.onload = (e) => {
                                const div = document.createElement('div');
                                div.className = 'relative aspect-square rounded-2xl overflow-hidden border border-slate-100 shadow-sm';
                                div.innerHTML = `<img src="${e.target.result}" class="w-full h-full object-cover">`;
                                previewGrid.appendChild(div);
                            }
                            reader.readAsDataURL(file);
                        });
                    }
                });
            }

            // Edit Preview
            const imageInputEdit = document.getElementById('imageInputEdit');
            const imagePreviewEdit = document.getElementById('imagePreviewEdit');
            if (imageInputEdit) {
                imageInputEdit.addEventListener('change', function() {
                    const file = this.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = (e) => imagePreviewEdit.src = e.target.result;
                        reader.readAsDataURL(file);
                    }
                });
            }
        });
    </script>
@endsection