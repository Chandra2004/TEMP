<nav class="flex-1 space-y-2 overflow-y-auto pr-2 custom-scrollbar">
    {{-- 1. UMUM (Akses Semua Role) --}}
    <a href="{{ url('/') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition group hover:bg-slate-50">
        <i data-lucide="panel-top" class="w-5 h-5 text-slate-500 group-hover:text-ksc-blue"></i>
        <span class="text-sm font-medium text-slate-700">Homepage</span>
    </a>

    <div class="my-4 border-t border-slate-100 pt-4">
        <p class="px-2 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-4 text-left">Main Menu</p>

        {{-- Dashboard: Semua Role --}}
        <a href="{{ url('/' . $user->nama_role . '/dashboard') }}"
            class="{{ request()->is('*/dashboard') && !request()->is('*/dashboard/*') ? 'sidebar-item-active shadow-lg shadow-blue-100' : '' }} flex items-center gap-3 px-4 py-3 rounded-xl transition group hover:bg-slate-50">
            <i data-lucide="layout-dashboard"
                class="w-5 h-5 {{ request()->is('*/dashboard') && !request()->is('*/dashboard/*') ? 'text-white' : 'text-slate-500 group-hover:text-ksc-blue' }}"></i>
            <span class="text-sm font-medium">Dashboard</span>
        </a>

        {{-- Event Menu for Atlet/Members --}}
        @if ($user->can('register-event'))
            <a href="{{ url('/' . $user->nama_role . '/dashboard/event') }}"
                class="{{ request()->is('*/dashboard/event*') ? 'sidebar-item-active shadow-lg shadow-blue-100' : '' }} flex items-center gap-3 px-4 py-3 rounded-xl transition group hover:bg-slate-50">
                <i data-lucide="calendar-check"
                    class="w-5 h-5 {{ request()->is('*/dashboard/event*') ? 'text-white' : 'text-slate-500 group-hover:text-ksc-blue' }}"></i>
                <span class="text-sm font-medium">Event</span>
            </a>

            <a href="{{ url('/' . $user->nama_role . '/dashboard/registration-history') }}"
                class="{{ request()->is('*/dashboard/registration-history*') ? 'sidebar-item-active shadow-lg shadow-blue-100' : '' }} flex items-center gap-3 px-4 py-3 rounded-xl transition group hover:bg-slate-50">
                <i data-lucide="history"
                    class="w-5 h-5 {{ request()->is('*/dashboard/registration-history*') ? 'text-white' : 'text-slate-500 group-hover:text-ksc-blue' }}"></i>
                <span class="text-sm font-medium">Riwayat Pendaftaran</span>
            </a>
        @endif
    </div>

    @if ($user->can('manage-payments'))
        <div x-data="{ open: {{ request()->is('*/management-payment*') ? 'true' : 'false' }} }">
            <button @click="open = !open"
                class="w-full flex items-center justify-between gap-3 px-4 py-3 rounded-xl transition group hover:bg-slate-50">
                <div class="flex items-center gap-3 text-left">
                    <i data-lucide="credit-card" class="w-5 h-5 text-slate-500 group-hover:text-ksc-blue"></i>
                    <span class="text-sm font-medium text-slate-700">Keuangan</span>
                </div>
                <i data-lucide="chevron-down" class="w-4 h-4 text-slate-400 transition-transform duration-300"
                    :class="open ? 'rotate-180' : ''"></i>
            </button>

            <div x-show="open" x-cloak x-collapse class="mt-1 ml-4 pl-4 border-l-2 border-slate-100 space-y-1">
                <a href="{{ url('/' . $user->nama_role . '/dashboard/management-payment') }}"
                    class="{{ request()->is('*/management-payment') ? 'text-ksc-blue font-black' : 'text-slate-500 hover:text-ksc-blue font-medium' }} block py-2 text-xs transition text-left">
                    Metode Pembayaran
                </a>
            </div>
        </div>
    @endif

    @if ($user->can('manage-events') || $user->can('manage-categories'))
        <div x-data="{ open: {{ request()->is('*/management-category*') ||
        request()->is('*/management-requirement-parameter*') ||
        request()->is('*/management-event*') ||
        request()->is('*/management-registration*') ||
        request()->is('*/management-result*') ||
        request()->is('*/management-gallery*')
            ? 'true'
            : 'false' }} }">
            <button @click="open = !open"
                class="w-full flex items-center justify-between gap-3 px-4 py-3 rounded-xl transition group hover:bg-slate-50">
                <div class="flex items-center gap-3 text-left">
                    <i data-lucide="calendar-range" class="w-5 h-5 text-slate-500 group-hover:text-ksc-blue"></i>
                    <span class="text-sm font-medium text-slate-700">Layanan Event</span>
                </div>
                <i data-lucide="chevron-down" class="w-4 h-4 text-slate-400 transition-transform duration-300"
                    :class="open ? 'rotate-180' : ''"></i>
            </button>

            <div x-show="open" x-cloak x-collapse class="mt-1 ml-4 pl-4 border-l-2 border-slate-100 space-y-1">
                
                @if ($user->can('manage-categories'))
                <a href="{{ url('/' . $user->nama_role . '/dashboard/management-category') }}"
                    class="{{ request()->is('*/management-category') || request()->is('*/management-category/page/*') ? 'text-ksc-blue font-black' : 'text-slate-500 hover:text-ksc-blue font-medium' }} block py-2 text-xs transition text-left">
                    Manajemen Gaya
                </a>
                
                <a href="{{ url('/' . $user->nama_role . '/dashboard/management-requirement-parameter') }}"
                    class="{{ request()->is('*/management-requirement-parameter') ? 'text-ksc-blue font-black' : 'text-slate-500 hover:text-ksc-blue font-medium' }} block py-2 text-xs transition text-left">
                    Master Parameter Lomba
                </a>
                @endif 
               
                
                @if ($user->can('manage-events'))
                <a href="{{ url('/' . $user->nama_role . '/dashboard/management-event') }}"
                    class="{{ request()->is('*/management-event') || request()->is('*/management-event/page/*') ? 'text-ksc-blue font-black' : 'text-slate-500 hover:text-ksc-blue font-medium' }} block py-2 text-xs transition text-left">
                    Manajemen Event
                </a>
                <a href="{{ url('/' . $user->nama_role . '/dashboard/management-registration') }}"
                    class="{{ request()->is('*/management-registration') ? 'text-ksc-blue font-black' : 'text-slate-500 hover:text-ksc-blue font-medium' }} block py-2 text-xs transition text-left">
                    Manajemen Pendaftaran
                </a>
                <a href="{{ url('/' . $user->nama_role . '/dashboard/management-result') }}"
                    class="{{ request()->is('*/management-result*') ? 'text-ksc-blue font-black' : 'text-slate-500 hover:text-ksc-blue font-medium' }} block py-2 text-xs transition text-left">
                    Manajemen Hasil Lomba
                </a>
                @endif
                
                @if ($user->can('manage-gallery'))
                <a href="{{ url('/' . $user->nama_role . '/dashboard/management-gallery') }}"
                    class="{{ request()->is('*/management-gallery') ? 'text-ksc-blue font-black' : 'text-slate-500 hover:text-ksc-blue font-medium' }} block py-2 text-xs transition text-left">
                    Manajemen Galeri
                </a>
                @endif
            </div>
        </div>
    @endif

    @if ($user->can('manage-users'))
        <div x-data="{ open: {{ request()->is('*/management-user*') || request()->is('*/management-coach*') || request()->is('*/management-member*') ? 'true' : 'false' }} }">
            <button @click="open = !open"
                class="w-full flex items-center justify-between gap-3 px-4 py-3 rounded-xl transition group hover:bg-slate-50">
                <div class="flex items-center gap-3 text-left">
                    <i data-lucide="users-round" class="w-5 h-5 text-slate-500 group-hover:text-ksc-blue"></i>
                    <span class="text-sm font-medium text-slate-700">SDM & Anggota</span>
                </div>
                <i data-lucide="chevron-down" class="w-4 h-4 text-slate-400 transition-transform duration-300"
                    :class="open ? 'rotate-180' : ''"></i>
            </button>

            <div x-show="open" x-cloak x-collapse class="mt-1 ml-4 pl-4 border-l-2 border-slate-100 space-y-1">
                <a href="{{ url('/' . $user->nama_role . '/dashboard/management-user') }}"
                    class="{{ request()->is('*/management-user') ? 'text-ksc-blue font-black' : 'text-slate-500 hover:text-ksc-blue font-medium' }} block py-2 text-xs transition text-left">
                    Manajemen Pengguna
                </a>
                <a href="{{ url('/' . $user->nama_role . '/dashboard/management-coach') }}"
                    class="{{ request()->is('*/management-coach') ? 'text-ksc-blue font-black' : 'text-slate-500 hover:text-ksc-blue font-medium' }} block py-2 text-xs transition text-left">
                    Manajemen Pelatih
                </a>
                <a href="{{ url('/' . $user->nama_role . '/dashboard/management-member') }}"
                    class="{{ request()->is('*/management-member') ? 'text-ksc-blue font-black' : 'text-slate-500 hover:text-ksc-blue font-medium' }} block py-2 text-xs transition text-left">
                    Manajemen Member
                </a>
            </div>
        </div>
    @endif

    @if ($user->can('export-reports'))
        <a href="{{ url('/' . $user->nama_role . '/dashboard/export-reports') }}"
            class="{{ request()->is('*/export-reports') ? 'sidebar-item-active shadow-lg shadow-blue-100' : '' }} flex items-center gap-3 px-4 py-3 rounded-xl transition group hover:bg-slate-50">
            <i data-lucide="file-spreadsheet"
                class="w-5 h-5 {{ request()->is('*/export-reports') ? 'text-white' : 'text-slate-500 group-hover:text-ksc-blue' }}"></i>
            <span class="text-sm font-medium">Laporan & Export</span>
        </a>
    @endif

    {{-- 5. PERSONAL MENU (Semua Role) --}}
    <div class="my-4 border-t border-slate-100 pt-4">
        <p class="px-2 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-4 text-left">Personal Menu
        </p>

        <a href="{{ url('/' . $user->nama_role . '/dashboard/notifications') }}"
            class="{{ request()->is('*/notifications') ? 'sidebar-item-active shadow-lg shadow-blue-100' : '' }} flex items-center gap-3 px-4 py-3 rounded-xl transition group hover:bg-slate-50">
            <i data-lucide="bell"
                class="w-5 h-5 {{ request()->is('*/notifications') ? 'text-white' : 'text-slate-500 group-hover:text-ksc-blue' }}"></i>
            <span class="text-sm font-medium">Notifikasi</span>
        </a>

        <a href="{{ url('/' . $user->nama_role . '/dashboard/my-profile') }}"
            class="{{ request()->is('*/my-profile') ? 'sidebar-item-active shadow-lg shadow-blue-100' : '' }} flex items-center gap-3 px-4 py-3 rounded-xl transition group hover:bg-slate-50 text-left">
            <i data-lucide="user-circle"
                class="w-5 h-5 {{ request()->is('*/my-profile') ? 'text-white' : 'text-slate-500 group-hover:text-ksc-blue' }}"></i>
            <span class="text-sm font-medium">Profil Saya</span>
        </a>
    </div>
</nav>

<style>
    /* Styling Active State (Sesuaikan dengan class utama Anda) */
    .sidebar-item-active {
        background-color: #0061ff;
        /* Warna KSC Blue */
        color: white !important;
    }

    .sidebar-item-active i {
        color: white !important;
    }

    .custom-scrollbar::-webkit-scrollbar {
        width: 4px;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #f1f5f9;
        border-radius: 10px;
    }
</style>
