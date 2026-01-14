<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - {{ $sys_settings['school_name'] ?? 'EZBorrow' }}</title>
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/2.6.0/uicons-regular-straight/css/uicons-regular-straight.css'>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        :root {
            --primary-color: {{ $sys_settings['primary_color'] ?? '#0d6efd' }};
            --secondary-color: {{ $sys_settings['secondary_color'] ?? '#ffc107' }};
            --sidebar-width: 280px;
        }
        
        body { font-family: 'Poppins', sans-serif; background-color: #f4f7fe; overflow-x: hidden; color: #334155; }

        .sidebar {
            width: var(--sidebar-width);
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            background-color: #ffffff;
            border-right: 1px solid rgba(0,0,0,0.05);
            z-index: 1050;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            flex-direction: column;
        }

        .sidebar-brand {
            padding: 2rem 1.5rem;
            text-align: center;
            border-bottom: 1px solid #f1f5f9;
        }

        .brand-logo {
            height: 60px;
            width: 60px;
            object-fit: contain;
            background: #f8fafc;
            padding: 8px;
            border-radius: 16px;
            margin-bottom: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .sidebar-nav {
            flex: 1;
            padding: 1.5rem 0.75rem;
            overflow-y: auto;
        }

        .nav-label {
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 1.2px;
            font-weight: 700;
            color: #94a3b8;
            margin: 1.5rem 1rem 0.5rem;
        }

        .nav-link {
            color: #64748b;
            padding: 10px 16px;
            display: flex;
            align-items: center;
            border-radius: 12px;
            margin-bottom: 4px;
            transition: all 0.2s;
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 500;
        }

        .nav-link i { font-size: 1.25rem; margin-right: 12px; transition: 0.2s; }
        .nav-link:hover { background-color: #f1f5f9; color: var(--primary-color); }

        .nav-link.active { 
            background-color: var(--primary-color) !important; 
            color: white !important; 
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }

        .main-content {
            margin-left: var(--sidebar-width);
            padding: 30px;
            min-height: 100vh;
        }

        .full-content { margin-left: 0 !important; }

        @media (max-width: 991.98px) {
            .sidebar { margin-left: calc(-1 * var(--sidebar-width)); }
            .main-content { margin-left: 0; padding: 20px; }
            .sidebar.show { margin-left: 0; }
        }
        
        @yield('css')
    </style>
</head>
<body>

    @php
        $isPublicPage = request()->routeIs('index') || 
                        request()->routeIs('pinjam.*') || 
                        request()->routeIs('scan.*') ||
                        request()->is('pinjam') || 
                        request()->is('scan-kamera') || 
                        request()->is('scan-manual');
    @endphp

    @auth
        @if(!$isPublicPage)
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-brand">
                @if(isset($sys_settings['school_logo']))
                    <img src="{{ asset($sys_settings['school_logo']) }}" alt="Logo" class="brand-logo">
                @endif
                <h5 class="fw-bold text-dark mb-0">{{ $sys_settings['app_name'] ?? 'EZBorrow' }}</h5>
                <p class="text-muted mb-0" style="font-size: 0.75rem;">{{ $sys_settings['school_name'] ?? 'Admin Panel' }}</p>
            </div>
            
            <div class="sidebar-nav">
                <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <i class="bi bi-grid-1x2-fill"></i> Dashboard
                </a>
                
                <div class="nav-label">Master Data</div>
                <a href="{{ route('siswa.index') }}" class="nav-link {{ request()->routeIs('siswa.*') ? 'active' : '' }}">
                    <i class="bi bi-people-fill"></i> Data Siswa
                </a>
                <a href="{{ route('kelas.index') }}" class="nav-link {{ request()->routeIs('kelas.*') ? 'active' : '' }}">
                    <i class="bi bi-door-open-fill"></i> Data Kelas
                </a>
                <a href="{{ route('chromebook.index') }}" class="nav-link {{ request()->routeIs('chromebook.*') ? 'active' : '' }}">
                    <i class="bi bi-laptop-fill"></i> Chromebook
                </a>
                <a href="{{ route('voucher.index') }}" class="nav-link {{ request()->routeIs('voucher.*') ? 'active' : '' }}">
                    <i class="bi bi-ticket-perforated-fill"></i> Stok Voucher
                </a>
                <a href="{{ route('promotion.index') }}" class="nav-link {{ request()->routeIs('promotion.*') ? 'active' : '' }}">
                    <i class="bi bi-arrow-up-circle-fill"></i> Kenaikan Kelas
                </a>

                <div class="nav-label">Operasional</div>
                <a href="{{ route('pinjam.index') }}" class="nav-link">
                    <i class="bi bi-qr-code-scan"></i> Halaman Scan
                </a>
                <a href="{{ route('riwayat.index') }}" class="nav-link {{ request()->routeIs('riwayat.*') ? 'active' : '' }}">
                    <i class="bi bi-clock-history"></i> Riwayat & Laporan
                </a>
                
                <div class="nav-label">Sistem</div>
                <a href="{{ route('settings.index') }}" class="nav-link {{ request()->routeIs('settings.*') ? 'active' : '' }}">
                    <i class="bi bi-gear-fill"></i> Pengaturan
                </a>
                
                <form action="{{ route('logout') }}" method="POST" class="mt-2">
                    @csrf
                    <button type="submit" class="nav-link border-0 bg-transparent text-danger w-100" style="text-align: left;">
                        <i class="bi bi-box-arrow-right"></i> Keluar Aplikasi
                    </button>
                </form>
            </div>
        </aside>
        @endif
    @endauth

    <main class="{{ (Auth::check() && !$isPublicPage) ? 'main-content' : 'full-content' }}">
        @auth
            @if(!$isPublicPage)
            <div class="d-lg-none mb-4 d-flex align-items-center justify-content-between">
                <button class="btn btn-white shadow-sm rounded-3" id="toggleSidebar">
                    <i class="bi bi-list fs-4"></i>
                </button>
                <h6 class="fw-bold mb-0">{{ $sys_settings['app_name'] ?? 'EZBorrow' }}</h6>
                <div style="width: 40px;"></div>
            </div>
            @endif
        @endauth

        <div class="container-fluid">
            @yield('content')
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Sidebar Toggle
        const toggleBtn = document.getElementById('toggleSidebar');
        const sidebar = document.getElementById('sidebar');
        if(toggleBtn) {
            toggleBtn.addEventListener('click', () => {
                sidebar.classList.toggle('show');
            });
        }

        // Toast Config
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 5000,
            timerProgressBar: true
        });

        // Konfirmasi Hapus
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
                if (result.isConfirmed) {
                    document.getElementById(formId).submit();
                }
            });
        }

        // Script Notifikasi Session
        @if(session('success'))
            Toast.fire({
                icon: 'success',
                html: `{!! session('success') !!}`
            });
        @endif

        @if(session('error'))
            Toast.fire({
                icon: 'error',
                html: `{!! session('error') !!}`
            });
        @endif

        @if(session('info'))
            Toast.fire({
                icon: 'info',
                html: `{!! session('info') !!}`
            });
        @endif
    </script>

    @yield('js')
</body>
</html>