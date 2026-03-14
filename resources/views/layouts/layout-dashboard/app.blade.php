<!DOCTYPE html>
<html lang="id" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="shortcut icon" href="{{ url('/assets/ico/favicon.ico') }}" type="image/x-icon">
    <title>{{ $title ?? 'Dashboard ' . $user['nama_role'] }}</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <script src="https://unpkg.com/lucide@latest"></script>

    <link href="https://cdn.jsdelivr.net/npm/flowbite@4.0.1/dist/flowbite.min.css" rel="stylesheet" />

    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>

    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Poppins', 'sans-serif'],
                    },
                    colors: {
                        ksc: {
                            blue: '#1e40af',
                            dark: '#1e3a8a',
                            accent: '#f59e0b',
                            light: '#eff6ff',
                            slate: '#0f172a'
                        }
                    }
                }
            }
        }
    </script>

    <style>
        .sidebar-item-active {
            background: linear-gradient(90deg, rgba(30, 64, 175, 0.1) 0%, rgba(30, 64, 175, 0) 100%);
            border-left: 4px solid #1e40af;
            color: #1e40af;
            font-weight: 700;
        }

        .sidebar {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .stats-card:hover {
            transform: translateY(-5px);
            transition: all 0.3s ease;
        }
    </style>
</head>


<body class="bg-slate-50 text-slate-700 font-sans">
    @include('layouts.layout-partials.notification')
    <div class="flex min-h-screen">
        <aside id="sidebar"
            class="sidebar fixed inset-y-0 left-0 z-[60] w-72 bg-white border-r border-slate-200 transform -translate-x-full lg:translate-x-0 lg:static lg:inset-0">
            <div class="h-full flex flex-col p-6">
                <div class="flex items-center justify-between mb-10 px-2 text-left">
                    <div class="flex items-center text-center gap-3 w-full">
                        <img src="{{ url('/assets/ico/icon-bar.png') }}" class="w-[80px] mx-auto">
                    </div>
                    <button id="closeSidebar"
                        class="lg:hidden p-2 text-slate-400 hover:bg-slate-50 rounded-lg transition-colors">
                        <i data-lucide="x" class="w-6 h-6"></i>
                    </button>
                </div>

                @include('layouts.layout-dashboard.navbar')

                <div class="pt-6 border-t border-slate-100">
                    <a href="{{ url('/') . $user['nama_role'] . '/' . $user['uid'] . '/dashboard/logout' }}"
                        class="flex gap-2 text-red-600">
                        <i data-lucide="log-out" class="w-5 h-5 group-hover:-translate-x-1 transition"></i>
                        <span class="font-bold text-sm">Keluar Akun</span>
                    </a>
                </div>
            </div>
        </aside>

        <main class="flex-1 min-w-0 bg-slate-50 flex flex-col h-screen overflow-y-auto">
            <header
                class="h-20 flex-shrink-0 bg-white border-b border-slate-200 flex items-center justify-between px-4 md:px-8 sticky top-0 z-40">
                <div class="flex items-center gap-2 md:gap-4">
                    <button id="toggleSidebar" class="lg:hidden p-2 hover:bg-slate-100 rounded-lg">
                        <i data-lucide="menu" class="w-6 h-6"></i>
                    </button>
                    <div>
                        <h1 class="text-lg md:text-xl font-bold text-slate-900 leading-tight uppercase">Dashboard
                            {{ $user['nama_role'] }}</h1>
                    </div>
                </div>

                <div class="flex items-center gap-3 md:gap-4">

                    <div x-data="{ open: false }" @click.away="open = false" class="relative">

                        <div @click="open = !open"
                            class="h-10 w-10 bg-slate-100 rounded-full flex items-center justify-center text-slate-500 cursor-pointer hover:bg-slate-200 transition relative">
                            <i data-lucide="bell" class="w-5 h-5"></i>
                            @if ($totalUnreadNotification > 0)
                                <span
                                    class="absolute top-0 text-sm right-0 w-6 h-6 bg-red-500 rounded-full border-2 border-white flex items-center justify-center text-white text-[8px] font-bold">
                                    {{ $totalUnreadNotification }}
                                </span>
                            @endif
                        </div>

                        <div x-show="open" x-transition:enter="transition ease-out duration-100"
                            x-transition:enter-start="transform opacity-0 scale-95"
                            x-transition:enter-end="transform opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-75"
                            x-transition:leave-start="transform opacity-100 scale-100"
                            x-transition:leave-end="transform opacity-0 scale-95"
                            class="absolute top-full right-0 mt-2 w-80 bg-white rounded-lg shadow-xl border border-slate-200 z-20"
                            style="display: none;">

                            <div class="flex justify-between items-center p-3 border-b border-slate-100">
                                <h3 class="font-semibold text-slate-800 text-sm">Notifikasi</h3>
                                <a href="{{ url('/' . $user['nama_role'] . '/dashboard/notifications') }}"
                                    class="text-xs text-blue-500 hover:underline font-medium focus:outline-none">
                                    Lihat semua
                                </a>
                            </div>

                            <div class="max-h-96 overflow-y-auto">
                                @forelse($unReadNotification as $notification)
                                    <div class="flex items-start gap-3 p-3 hover:bg-slate-50 border-b border-slate-100">
                                        <div
                                            class="w-8 h-8 bg-blue-100 rounded-full flex-shrink-0 flex items-center justify-center">
                                            <i data-lucide="bell-ring" class="w-4 h-4 text-blue-500"></i>
                                        </div>
                                        <a href="#" class="flex-grow">
                                            <p class="text-sm text-slate-700 font-bold">{{ $notification['judul'] }}</p>
                                            <span
                                                class="font-normal text-slate-600 line-clamp-2 text-sm">{!! $notification['pesan'] !!}</span>
                                        </a>
                                        <button title="Tandai sudah dibaca"
                                            class="p-1 text-slate-400 hover:text-blue-500 rounded-full focus:outline-none focus:bg-blue-100">
                                            <i data-lucide="circle" class="w-3 h-3"></i>
                                        </button>
                                    </div>
                                @empty
                                    <div class="flex flex-col items-center justify-center py-8 px-4 text-center">
                                        <div
                                            class="w-12 h-12 bg-slate-50 rounded-full flex items-center justify-center mb-3">
                                            <i data-lucide="bell-off" class="w-6 h-6 text-slate-300"></i>
                                        </div>
                                        <p class="text-sm font-semibold text-slate-500">Tidak ada notifikasi terbaru</p>
                                        <p class="text-xs text-slate-400 mt-1">Kami akan mengabari Anda jika ada
                                            informasi baru.</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>



                    <div x-data="{ open: false }" @click.away="open = false" class="relative">

                        <div @click="open = !open"
                            class="flex items-center gap-2 md:gap-3 pl-2 md:pl-4 border-l border-slate-200 cursor-pointer">
                            <div class="text-right hidden sm:block">
                                <p class="text-sm font-bold text-slate-900">{{ $user['nama_lengkap'] }}</p>
                                <p class="text-[10px] text-slate-500 uppercase font-bold tracking-tight">
                                    {{ strtoupper($user['nama_role']) }}</p>
                            </div>
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($user['nama_lengkap']) }}&background=1e40af&color=fff"
                                class="h-9 w-9 md:h-10 md:w-10 rounded-xl border border-slate-200 shadow-sm">
                        </div>

                        <div x-show="open" x-transition:enter="transition ease-out duration-100"
                            x-transition:enter-start="transform opacity-0 scale-95"
                            x-transition:enter-end="transform opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-75"
                            x-transition:leave-start="transform opacity-100 scale-100"
                            x-transition:leave-end="transform opacity-0 scale-95"
                            class="absolute top-full right-0 mt-2 w-48 bg-white rounded-lg shadow-xl border border-slate-200 z-20 py-1"
                            style="display: none;">

                            <a href="{{ url('/' . strtolower($user['nama_role']) . '/dashboard/my-profile') }}"
                                class="flex items-center gap-3 px-4 py-2 text-sm text-slate-700 hover:bg-slate-100">
                                <i data-lucide="user-circle" class="w-4 h-4"></i>
                                <span>Profil Saya</span>
                            </a>

                            <div class="my-1 h-px bg-slate-100"></div>

                            <a href="{{ url('/') . $user['nama_role'] . '/' . $user['uid'] . '/dashboard/logout' }}"
                                class="flex items-center gap-3 px-4 py-2 text-sm text-red-600 hover:bg-red-50 font-medium">
                                <i data-lucide="log-out" class="w-4 h-4"></i>
                                <span>Keluar</span>
                            </a>
                        </div>

                    </div>

                </div>
            </header>

            @yield('dashboard-section')
        </main>
    </div>

    <div id="sidebarBackdrop"
        class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-[50] hidden lg:hidden transition-all duration-300 opacity-0">
    </div>

    <script>
        lucide.createIcons();

        // Responsive Sidebar Toggle Logic
        const sidebar = document.getElementById('sidebar');
        const toggleBtn = document.getElementById('toggleSidebar');
        const closeBtn = document.getElementById('closeSidebar');
        const backdrop = document.getElementById('sidebarBackdrop');

        function toggleSidebar() {
            const isOpen = !sidebar.classList.contains('-translate-x-full');

            if (isOpen) {
                // Close
                sidebar.classList.add('-translate-x-full');
                backdrop.classList.add('opacity-0');
                setTimeout(() => backdrop.classList.add('hidden'), 300);
                document.body.style.overflow = ''; // Enable scroll
            } else {
                // Open
                backdrop.classList.remove('hidden');
                setTimeout(() => backdrop.classList.remove('opacity-0'), 10);
                sidebar.classList.remove('-translate-x-full');
                document.body.style.overflow = 'hidden'; // Disable scroll
            }
        }

        toggleBtn?.addEventListener('click', (e) => {
            e.stopPropagation();
            toggleSidebar();
        });

        closeBtn?.addEventListener('click', toggleSidebar);
        backdrop?.addEventListener('click', toggleSidebar);

        // Sidebar Navigation Handling (Automatic close on mobile)
        const navLinks = sidebar.querySelectorAll('nav a');
        navLinks.forEach(link => {
            link.addEventListener('click', () => {
                if (window.innerWidth < 1024) {
                    toggleSidebar();
                }
            });
        });

        // Resize Handling
        window.addEventListener('resize', () => {
            if (window.innerWidth >= 1024) {
                sidebar.classList.remove('-translate-x-full');
                backdrop.classList.add('hidden');
                backdrop.classList.add('opacity-0');
                document.body.style.overflow = '';
            } else {
                sidebar.classList.add('-translate-x-full');
            }
        });
    </script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flowbite@4.0.1/dist/flowbite.min.js"></script>
</body>

</html>
