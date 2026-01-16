<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - {{ $sys_settings['school_name'] ?? 'EZBorrow' }}</title>
    
    {{-- Favicon --}}
    <link rel="icon" type="image/png" href="{{ asset('assets/img/favicon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('assets/img/favicon.png') }}">
    
    {{-- Styles --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    {{-- PWA Meta Tags --}}
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <meta name="theme-color" content="#4361ee">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">

    <script>
        // Mencegah 'flicker' putih pada dark mode
        const savedTheme = localStorage.getItem('theme') || 'light';
        document.documentElement.setAttribute('data-theme', savedTheme);
    </script>

    <style>
        :root {
            --primary-color: {{ $sys_settings['primary_color'] ?? '#0d6efd' }};
            --secondary-color: {{ $sys_settings['secondary_color'] ?? '#ffc107' }};
            --sidebar-width: 280px;
            /* Light Mode Variables */
            --bg-body: #f4f7fe;
            --bg-sidebar: #ffffff;
            --text-main: #334155;
            --text-muted: #94a3b8;
            --border-color: #f1f5f9;
            --card-bg: #ffffff;
        }

        [data-theme="dark"] {
            --bg-body: #0b1120;     /* Lebih gelap pekat untuk kontras */
            --bg-sidebar: #111827;
            --text-main: #f8fafc;   /* Putih bersih */
            --text-muted: #94a3b8;
            --border-color: #1f2937;
            --card-bg: #1e293b;
        }
        
        body { 
            font-family: 'Poppins', sans-serif; 
            background-color: var(--bg-body) !important; 
            color: var(--text-main) !important;
            overflow-x: hidden; 
            transition: background-color 0.3s ease;
        }

        /* PERBAIKAN KONTRAS DARK MODE */
        [data-theme="dark"] .text-dark, 
        [data-theme="dark"] h3, 
        [data-theme="dark"] h5, 
        [data-theme="dark"] h6,
        [data-theme="dark"] .fw-bold {
            color: #ffffff !important;
        }

        /* Sidebar Styling */
        .sidebar {
            width: var(--sidebar-width);
            height: 100vh;
            position: fixed;
            top: 0; left: 0;
            background-color: var(--bg-sidebar);
            border-right: 1px solid var(--border-color);
            z-index: 1050;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            flex-direction: column;
        }

        .sidebar-brand {
            padding: 1.5rem;
            text-align: center;
            border-bottom: 1px solid var(--border-color);
        }

        .brand-logo {
            height: 50px; width: 50px;
            object-fit: contain;
            background: #f8fafc;
            padding: 5px; border-radius: 12px;
            margin-bottom: 10px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .sidebar-nav {
            flex: 1; padding: 1rem 0.75rem; overflow-y: auto;
        }

        .nav-label {
            font-size: 0.65rem; text-transform: uppercase; letter-spacing: 1.2px;
            font-weight: 700; color: var(--text-muted); margin: 1.2rem 1rem 0.5rem;
        }

        .nav-link {
            color: var(--text-main); padding: 10px 16px; display: flex;
            align-items: center; border-radius: 12px; margin-bottom: 2px;
            transition: all 0.2s; text-decoration: none; font-size: 0.85rem;
            font-weight: 500; opacity: 0.8;
        }

        .nav-link i { font-size: 1.1rem; margin-right: 12px; }
        .nav-link:hover { background-color: rgba(67, 97, 238, 0.08); color: var(--primary-color); opacity: 1; }
        .nav-link.active { 
            background-color: var(--primary-color) !important; 
            color: white !important; opacity: 1;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .theme-switch-wrapper {
            padding: 1rem; border-top: 1px solid var(--border-color);
            display: flex; align-items: center; justify-content: space-between;
        }

        .main-content {
            margin-left: var(--sidebar-width); padding: 25px;
            min-height: 100vh; transition: all 0.3s;
        }

        .full-content { margin-left: 0 !important; width: 100% !important; }
        .card { background-color: var(--card-bg) !important; border-color: var(--border-color) !important; }

        @media (max-width: 991.98px) {
            .sidebar { margin-left: calc(-1 * var(--sidebar-width)); }
            .main-content { margin-left: 0; }
            .sidebar.show { margin-left: 0; box-shadow: 10px 0 30px rgba(0,0,0,0.1); }
        }

        .btn-primary { background-color: var(--primary-color) !important; border-color: var(--primary-color) !important; }
    </style>
    @yield('css')
</head>
<body>

    @php
        $isPublicPage = request()->routeIs('index') || request()->is('pinjam*') || request()->is('scan*') || request()->is('login');
    @endphp

    @auth
        @if(!$isPublicPage)
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-brand">
                @if(isset($sys_settings['school_logo']))
                    <img src="{{ asset($sys_settings['school_logo']) }}" alt="Logo" class="brand-logo">
                @endif
                <h6 class="fw-bold mb-0 text-dark">{{ $sys_settings['app_name'] ?? 'EZBorrow' }}</h6>
                <small class="text-muted">{{ $sys_settings['school_name'] ?? 'Admin Panel' }}</small>
            </div>
            
            <div class="sidebar-nav">
                <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <i class="bi bi-house-door-fill"></i> Dashboard
                </a>
                
                <div class="nav-label">Operasional</div>
                <a href="{{ route('pinjam.index') }}" class="nav-link">
                    <i class="bi bi-qr-code-scan"></i> Halaman Scan
                </a>
                <a href="{{ route('riwayat.index') }}" class="nav-link {{ request()->routeIs('riwayat.*') ? 'active' : '' }}">
                    <i class="bi bi-clock-history"></i> Riwayat & Laporan
                </a>

                <div class="nav-label">Master Data</div>
                <a href="{{ route('guru.index') }}" class="nav-link {{ request()->routeIs('guru.*') ? 'active' : '' }}">
                    <i class="bi bi-person-badge-fill"></i> Data Guru
                </a>
                <a href="{{ route('kelas.index') }}" class="nav-link {{ request()->routeIs('kelas.*') ? 'active' : '' }}">
                    <i class="bi bi-door-open-fill"></i> Data Kelas
                </a>
                <a href="{{ route('siswa.index') }}" class="nav-link {{ request()->routeIs('siswa.*') ? 'active' : '' }}">
                    <i class="bi bi-people-fill"></i> Data Siswa
                </a>
                <a href="{{ route('chromebook.index') }}" class="nav-link {{ request()->routeIs('chromebook.*') ? 'active' : '' }}">
                    <i class="bi bi-laptop-fill"></i> Data Chromebook
                </a>
                <a href="{{ route('voucher.index') }}" class="nav-link {{ request()->routeIs('voucher.*') ? 'active' : '' }}">
                    <i class="bi bi-ticket-perforated-fill"></i> Stok Voucher
                </a>
                <a href="{{ route('promotion.index') }}" class="nav-link {{ request()->routeIs('promotion.*') ? 'active' : '' }}">
                    <i class="bi bi-arrow-up-circle-fill"></i> Kenaikan Kelas
                </a>

                <div class="nav-label">Sistem</div>
                <a href="{{ route('settings.index') }}" class="nav-link {{ request()->routeIs('settings.*') ? 'active' : '' }}">
                    <i class="bi bi-gear-fill"></i> Pengaturan
                </a>

                {{-- Tombol Install PWA --}}
                <div id="install-banner" style="display: none;" class="px-3 mt-2">
                    <button id="btnInstall" class="btn btn-primary w-100 rounded-pill small py-2">
                        <i class="bi bi-download me-2"></i>Install App
                    </button>
                </div>
                
                <form action="{{ route('logout') }}" method="POST" class="mt-2">
                    @csrf
                    <button type="submit" class="nav-link border-0 bg-transparent text-danger w-100" style="text-align: left;">
                        <i class="bi bi-box-arrow-right"></i> Keluar Aplikasi
                    </button>
                </form>
            </div>

            <div class="theme-switch-wrapper">
                <small class="text-muted fw-bold"><i class="bi bi-circle-half me-2"></i> Tema</small>
                <button id="theme-toggle" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                    <i id="theme-icon" class="bi bi-moon-stars-fill"></i>
                </button>
            </div>
            
            <div class="p-3 text-center border-top border-light">
                <small class="text-muted" style="font-size: 0.6rem;">Developed by <span class="fw-bold">Fiqri Haikal</span></small>
            </div>
        </aside>
        @endif
    @endauth

    <main class="{{ (Auth::check() && !$isPublicPage) ? 'main-content' : 'full-content' }}">
        @auth @if(!$isPublicPage)
            <div class="d-lg-none mb-3 d-flex align-items-center justify-content-between p-2 bg-white rounded-3 shadow-sm">
                <button class="btn btn-white" id="toggleSidebar"><i class="bi bi-list fs-3"></i></button>
                <h6 class="fw-bold mb-0 text-dark">EZBorrow</h6>
                <div style="width: 40px;"></div>
            </div>
        @endif @endauth

        <div class="container-fluid">
            @yield('content')
        </div>
    </main>

    {{-- Scripts --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Sidebar Mobile Logic
        const toggleBtn = document.getElementById('toggleSidebar');
        const sidebar = document.getElementById('sidebar');
        if(toggleBtn) {
            toggleBtn.addEventListener('click', () => sidebar.classList.toggle('show'));
        }

        // Dark Mode Logic
        const themeToggle = document.getElementById('theme-toggle');
        const themeIcon = document.getElementById('theme-icon');
        const htmlEl = document.documentElement;

        function updateThemeUI(theme) {
            if (theme === 'dark') {
                themeIcon.className = 'bi bi-sun-fill text-warning';
                themeToggle.classList.replace('btn-outline-secondary', 'btn-outline-warning');
            } else {
                themeIcon.className = 'bi bi-moon-stars-fill';
                themeToggle.classList.replace('btn-outline-warning', 'btn-outline-secondary');
            }
        }

        updateThemeUI(htmlEl.getAttribute('data-theme'));

        if(themeToggle) {
            themeToggle.addEventListener('click', () => {
                const newTheme = htmlEl.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
                htmlEl.setAttribute('data-theme', newTheme);
                localStorage.setItem('theme', newTheme);
                updateThemeUI(newTheme);
            });
        }

        // Toast Notification
        const Toast = Swal.mixin({
            toast: true, position: 'top-end', showConfirmButton: false, timer: 3000, timerProgressBar: true
        });

        @if(!$isPublicPage) 
            @if(session('success')) Toast.fire({ icon: 'success', title: '{{ session("success") }}' }); @endif
            @if(session('error')) Toast.fire({ icon: 'error', title: '{{ session("error") }}' }); @endif
        @endif

        function confirmDelete(formId, itemName) {
            Swal.fire({
                title: 'Hapus Data?',
                html: `Anda akan menghapus <b>${itemName}</b> secara permanen.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                reverseButtons: true,
                customClass: { popup: 'rounded-4' }
            }).then((result) => {
                if (result.isConfirmed) document.getElementById(formId).submit();
            });
        }

        // PWA Installation Logic
        let deferredPrompt;
        const installBanner = document.getElementById('install-banner');
        const btnInstall = document.getElementById('btnInstall');

        window.addEventListener('beforeinstallprompt', (e) => {
            e.preventDefault();
            deferredPrompt = e;
            if(installBanner) installBanner.style.display = 'block';
        });

        if(btnInstall) {
            btnInstall.addEventListener('click', async () => {
                if (deferredPrompt) {
                    deferredPrompt.prompt();
                    const { outcome } = await deferredPrompt.userChoice;
                    if (outcome === 'accepted') {
                        installBanner.style.display = 'none';
                    }
                    deferredPrompt = null;
                }
            });
        }

        // Service Worker Registration
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js')
                    .then(reg => console.log('Service Worker Active'))
                    .catch(err => console.error('Service Worker Error', err));
            });
        }
    </script>
    @yield('js')
</body>
</html>