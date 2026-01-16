@extends('layouts.app')

@section('title', 'Login Admin')

@section('css')
<style>
    /* Background animatif yang halus */
    body {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    }

    .login-card {
        border: none;
        border-radius: 24px;
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(10px);
        box-shadow: 0 20px 40px rgba(0,0,0,0.05);
        transition: transform 0.3s ease;
    }

    .login-card:hover {
        transform: translateY(-5px);
    }

    .form-control {
        border-radius: 12px;
        padding: 12px 15px;
        border: 1px solid #e2e8f0;
        background-color: #f8fafc;
        transition: all 0.3s;
    }

    .form-control:focus {
        background-color: #fff;
        border-color: var(--primary-color);
        box-shadow: 0 0 0 4px rgba(13, 110, 253, 0.1);
    }

    /* Styling untuk Input Group (Fitur Show Password) */
    .input-group-text {
        background-color: #f8fafc;
        border: 1px solid #e2e8f0;
        border-left: none;
        border-radius: 0 12px 12px 0;
        color: #64748b;
        cursor: pointer;
        transition: 0.3s;
    }

    .input-group-text:hover {
        color: var(--primary-color);
    }

    .password-input {
        border-right: none;
    }

    .btn-login {
        background: linear-gradient(135deg, var(--primary-color) 0%, #1e40af 100%);
        border: none;
        padding: 14px;
        border-radius: 100px;
        font-weight: 700;
        letter-spacing: 0.5px;
        box-shadow: 0 10px 20px rgba(30, 64, 175, 0.2);
        transition: all 0.3s;
    }

    .btn-login:hover {
        transform: scale(1.02);
        box-shadow: 0 15px 25px rgba(30, 64, 175, 0.3);
    }

    .brand-logo {
        width: 60px;
        height: 60px;
        object-fit: contain;
        margin-bottom: 15px;
    }
</style>
@endsection

@section('content')
<div class="container">
    <div class="row justify-content-center align-items-center" style="min-height: 90vh;">
        <div class="col-md-5 col-lg-4">
            
            <div class="text-center mb-4 animate__animated animate__fadeIn">
                @if(isset($sys_settings['school_logo']))
                    <img src="{{ asset($sys_settings['school_logo']) }}" class="brand-logo" alt="Logo">
                @endif
                <h2 class="fw-bold text-dark mb-1">
                    <span class="text-primary">{{ $sys_settings['school_name'] ?? 'EZBorrow' }}</span> Admin
                </h2>
                <p class="text-muted small">Kelola peminjaman dengan sistem cerdas</p>
            </div>

            <div class="card login-card animate__animated animate__zoomIn">
                <div class="card-body p-4 p-md-5">
                    
                    @if(session('error'))
                    <div class="alert alert-danger border-0 small rounded-3 mb-4">
                        <i class="bi bi-exclamation-circle me-2"></i> {{ session('error') }}
                    </div>
                    @endif

                    <form action="{{ route('login.post') }}" method="POST">
                        @csrf
                        
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-secondary">Alamat Email</label>
                            <div class="input-group">
                                <input type="email" name="email" class="form-control" placeholder="nama@email.com" required autofocus>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label small fw-bold text-secondary">Kata Sandi</label>
                            <div class="input-group">
                                <input type="password" name="password" id="passwordField" class="form-control password-input" placeholder="••••••••" required>
                                <span class="input-group-text" id="togglePassword">
                                    <i class="bi bi-eye-fill" id="eyeIcon"></i>
                                </span>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 btn-login text-uppercase mb-3">
                            Login <i class="bi bi-arrow-right-short ms-1"></i>
                        </button>

                        <div class="text-center">
                            <a href="{{ route('index') }}" class="text-decoration-none small text-muted hover-primary">
                                <i class="bi bi-house-door me-1"></i> Kembali ke Beranda
                            </a>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="text-center mt-4">
                <div class="d-flex align-items-center justify-content-center gap-2 mb-2">
                    <div style="height: 1px; width: 20px; background: #cbd5e1;"></div>
                    <span class="text-muted small fw-bold" style="letter-spacing: 1px;">EZBorrow</span>
                    <div style="height: 1px; width: 20px; background: #cbd5e1;"></div>
                </div>
                <p class="text-muted mb-0" style="font-size: 0.8rem;">
                    Sistem dikembangkan oleh <span class="text-dark fw-bold">Fiqri Haikal</span>
                </p>
                <p class="text-muted small" style="font-size: 0.7rem;">
                    &copy; {{ date('Y') }} All Rights Reserved.
                </p>
            </div>
            
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
    // Fitur Toggle Show/Hide Password
    const togglePassword = document.querySelector('#togglePassword');
    const passwordField = document.querySelector('#passwordField');
    const eyeIcon = document.querySelector('#eyeIcon');

    togglePassword.addEventListener('click', function () {
        // Toggle tipe input
        const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordField.setAttribute('type', type);
        
        // Toggle ikon
        eyeIcon.classList.toggle('bi-eye-fill');
        eyeIcon.classList.toggle('bi-eye-slash-fill');
        
        // Animasi feedback kecil
        this.style.transform = 'scale(0.9)';
        setTimeout(() => {
            this.style.transform = 'scale(1)';
        }, 100);
    });
</script>
@endsection