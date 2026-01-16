@extends('layouts.app')

@section('title', 'Solusi Pinjam Chromebook')

@section('css')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<style>
/* =========================
    HERO SECTION
========================= */
.hero-gradient {
    background:
        radial-gradient(circle at top right, rgba(13,110,253,.08), transparent),
        radial-gradient(circle at bottom left, rgba(255,193,7,.08), transparent);
    background-color: #ffffff;
    padding: 60px 0 100px;
    overflow: hidden;
}

.display-1 {
    letter-spacing: -2px;
    line-height: 1.1;
    font-weight: 800;
    font-size: clamp(2.4rem, 6vw, 4.8rem);
}

/* =========================
    HERO IMAGE & ANIMATION
========================= */
.hero-image {
    animation: float 5s ease-in-out infinite;
    filter: drop-shadow(0 30px 60px rgba(13,110,253,.18));
    width: 100%;
    height: auto;
}

@keyframes float {
    0%,100% { transform: translateY(0); }
    50% { transform: translateY(-20px) rotate(1deg); }
}

/* =========================
    FEATURE CARD
========================= */
.feature-card {
    border: none;
    border-radius: 25px;
    background: #fff;
    transition: all .4s cubic-bezier(.4,0,.2,1);
}

.feature-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 30px 60px rgba(0,0,0,.08);
}

.feature-icon-wrapper {
    width: 80px;
    height: 80px;
    font-size: 2.5rem; /* Ukuran icon diperbesar */
    transition: .4s;
    display: flex;
    align-items: center;
    justify-content: center;
}

.feature-card:hover .feature-icon-wrapper {
    transform: scale(1.1) rotate(10deg);
}

/* =========================
    NAVBAR & OTHERS
========================= */
.navbar-landing {
    backdrop-filter: blur(15px);
    background: rgba(255,255,255,.85);
    border-bottom: 1px solid rgba(0,0,0,.05);
}

.btn-main {
    padding: 16px 40px;
    font-weight: 700;
    border-radius: 100px;
}

/* Responsive */
@media (max-width: 991px) {
    .hero-gradient { text-align: center; }
    .hero-image { margin: 0 auto; max-height: 400px; }
    .btn-main { width: 100%; }
}
</style>
@endsection

@section('content')
<nav class="navbar navbar-expand-lg navbar-landing sticky-top py-3">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="#">
            @if(isset($sys_settings['school_logo']) && $sys_settings['school_logo'] != '')
                <img src="{{ asset($sys_settings['school_logo']) }}" class="me-2 shadow-sm rounded" style="max-height: 40px;">
            @else
                <div class="bg-primary rounded me-2 d-flex align-items-center justify-content-center" style="width:40px;height:40px;">
                    <i class="bi bi-laptop text-white fs-5"></i>
                </div>
            @endif
            <div>
                <span class="fw-bold fs-5 d-block">{{ $sys_settings['app_name'] ?? 'EZBorrow' }}</span>
                <small class="text-muted d-none d-sm-block">{{ $sys_settings['school_name'] ?? 'Sistem Inventaris Sekolah' }}</small>
            </div>
        </a>

        <div class="ms-auto">
            @auth
                <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-primary rounded-pill fw-bold">
                    <i class="bi bi-grid-fill me-1"></i> Admin Panel
                </a>
            @else
                <a href="{{ route('login') }}" class="btn btn-primary rounded-pill fw-bold">
                    <i class="bi bi-person-circle me-1"></i> Login
                </a>
            @endauth
        </div>
    </div>
</nav>

<section class="hero-gradient">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-6 order-2 order-lg-1">
                <h1 class="display-1 mb-4">
                    Solusi Cerdas<br>
                    <span class="text-primary">Pinjam Chromebook.</span>
                </h1>
                <p class="lead text-muted mb-4">
                    Transformasi digital peminjaman perangkat sekolah.
                    Cepat dengan <b>QR Code</b> dan aman dengan <b>Voucher</b>.
                </p>
                <a href="{{ route('pinjam.index') }}" class="btn btn-primary btn-main shadow-lg">
                    Mulai Peminjaman <i class="bi bi-arrow-right-circle ms-2"></i>
                </a>
            </div>
            <div class="col-lg-6 order-1 order-lg-2 text-center">
                <img src="{{ asset('img/hero-chromebook.png') }}" alt="Hero" class="hero-image img-fluid">
            </div>
        </div>
    </div>
</section>

<section class="py-5 bg-white">
    <div class="container py-lg-5">
        <div class="text-center mb-5">
            <h2 class="fw-bold">Keunggulan Sistem</h2>
            <p class="text-muted">Didesain untuk efisiensi manajemen IT sekolah.</p>
        </div>

        <div class="row g-4">
            <div class="col-md-4">
                <div class="card feature-card h-100 p-4 shadow-sm text-center border">
                    <div class="feature-icon-wrapper bg-secondary bg-opacity-10 text-primary rounded-circle mx-auto mb-4">
                        <i class="bi bi-qr-code-scan"></i>
                    </div>
                    <h4 class="fw-bold">Scan QR</h4>
                    <p class="text-muted">Validasi cepat tanpa input manual.</p>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card feature-card h-100 p-4 shadow-sm text-center border">
                    <div class="feature-icon-wrapper bg-warning bg-opacity-10 text-warning rounded-circle mx-auto mb-4">
                        <i class="bi bi-ticket-perforated"></i>
                    </div>
                    <h4 class="fw-bold">Voucher</h4>
                    <p class="text-muted">Distribusi internet otomatis.</p>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card feature-card h-100 p-4 shadow-sm text-center border">
                    <div class="feature-icon-wrapper bg-info bg-opacity-10 text-info rounded-circle mx-auto mb-4">
                        <i class="bi bi-graph-up-arrow"></i>
                    </div>
                    <h4 class="fw-bold">Laporan</h4>
                    <p class="text-muted">Monitoring real-time & akurat.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<footer class="py-5 border-top bg-light">
    <div class="container text-center">
        <p class="text-muted small mb-1">
            &copy; {{ date('Y') }} {{ $sys_settings['school_name'] ?? 'Sekolah' }}
        </p>
        <p class="text-muted small">
            Dikembangkan oleh <b>Fiqri Haikal</b>
        </p>
    </div>
</footer>
@endsection