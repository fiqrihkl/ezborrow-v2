@extends('layouts.app')

@section('title', 'Pilih Metode Scan')

@section('css')
<style>
    /* Latar belakang lembut untuk memfokuskan kartu */
    body {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    }

    /* Styling Kartu Metode */
    .card-method { 
        transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
        cursor: pointer; 
        border: none; 
        border-radius: 24px;
        overflow: hidden;
        background: #ffffff;
    }

    .card-method:hover { 
        transform: translateY(-12px); 
        box-shadow: 0 20px 40px rgba(0,0,0,0.08) !important; 
    }

    /* Kotak Ikon yang estetik */
    .icon-wrapper {
        width: 100px;
        height: 100px;
        margin: 0 auto 25px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 20px;
        font-size: 3rem;
        transition: 0.3s;
    }

    .card-method:hover .icon-wrapper {
        transform: scale(1.1) rotate(5deg);
    }

    /* Animasi sederhana untuk teks */
    .method-title {
        letter-spacing: -0.5px;
        color: #2d3436;
    }

    /* Tombol Rounded yang tebal */
    .btn-action {
        font-weight: 600;
        letter-spacing: 0.5px;
        text-transform: uppercase;
        font-size: 0.8rem;
        transition: 0.3s;
    }

    .card-method:hover .btn-action {
        padding-left: 30px;
        padding-right: 30px;
    }
</style>
@endsection

@section('content')
<div class="container min-vh-100 d-flex flex-column justify-content-center align-items-center py-5">
    <div class="text-center mb-5">
        <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill mb-3 fw-bold">PROSES SCAN</span>
        <h1 class="fw-bold method-title display-5">Metode Pemindaian</h1>
        <p class="text-muted mx-auto" style="max-width: 500px;">Pilih cara terbaik untuk memasukkan data QR Code perangkat Chromebook Anda ke dalam sistem.</p>
    </div>

    <div class="row w-100 justify-content-center g-4">
        <div class="col-md-5 col-lg-4">
            <div class="card card-method shadow-sm p-4 text-center h-100 border-top border-dark border-5">
                <div class="card-body d-flex flex-column">
                    <div class="icon-wrapper bg-dark bg-opacity-10 text-dark">
                        <i class="bi bi-upc-scan"></i>
                    </div>
                    <h3 class="fw-bold mb-3">Scanner Laser</h3>
                    <p class="text-muted flex-grow-1 mb-4">Ideal untuk scan cepat dalam jumlah banyak menggunakan alat <strong>Barcode Scanner USB</strong> eksternal.</p>
                    <a href="{{ url('/scan-manual') }}" class="btn btn-dark btn-action w-100 rounded-pill py-3">
                        Gunakan Scanner <i class="bi bi-arrow-right ms-2"></i>
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-5 col-lg-4">
            <div class="card card-method shadow-sm p-4 text-center h-100 border-top border-primary border-5">
                <div class="card-body d-flex flex-column">
                    <div class="icon-wrapper bg-secondary bg-opacity-10 text-primary">
                        <i class="bi bi-camera"></i>
                    </div>
                    <h3 class="fw-bold mb-3">Kamera Web</h3>
                    <p class="text-muted flex-grow-1 mb-4">Gunakan kamera bawaan <strong>Laptop atau HP</strong>. Lebih praktis tanpa perlu kabel tambahan.</p>
                    <a href="{{ url('/scan-kamera') }}" class="btn btn-primary btn-action w-100 rounded-pill py-3 shadow-sm">
                        Gunakan Kamera <i class="bi bi-camera-fill ms-2"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-5">
        <a href="{{ url('/') }}" class="btn btn-outline-secondary border-0 rounded-pill px-4 py-2 fw-bold">
            <i class="bi bi-house-door me-2"></i> Kembali ke Beranda
        </a>
    </div>
</div>
@endsection