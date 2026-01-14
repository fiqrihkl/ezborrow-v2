@extends('layouts.app')

@section('title', 'Smart Pengembalian')

@section('css')
<style>
    :root { --primary-color: #198754; --soft-bg: #f8fafc; }
    
    .scan-page-wrapper {
        position: fixed; top: 0; left: 0; width: 100vw; height: 100vh;
        display: flex; align-items: center; justify-content: center;
        background: radial-gradient(circle at center, rgba(25, 135, 84, 0.05) 0%, var(--soft-bg) 100%);
    }

    .unified-card {
        width: 400px; height: 620px; 
        background: white; border-radius: 45px;
        padding: 35px 30px;
        box-shadow: 0 25px 80px rgba(0, 0, 0, 0.08);
        border: 1px solid rgba(25, 135, 84, 0.05);
        display: flex; flex-direction: column;
        position: relative;
    }

    .info-badge { background: var(--primary-color); color: white; border-radius: 25px; padding: 20px; margin-bottom: 25px; text-align: center; }
    
    .scanner-view { position: relative; width: 220px; height: 220px; margin: 10px auto; border-radius: 35px; overflow: hidden; background: #000; border: 5px solid #fff; box-shadow: 0 10px 30px rgba(0,0,0,0.05); }
    .laser { position: absolute; width: 100%; height: 3px; background: var(--primary-color); box-shadow: 0 0 15px var(--primary-color); animation: laserAnim 3s infinite ease-in-out; z-index: 10; }
    
    @keyframes laserAnim { 0%, 100% { top: 10%; } 50% { top: 90%; } }

    #reader-kembali { width: 100% !important; height: 100% !important; }
    video { object-fit: cover !important; }

    .system-footer { margin-top: auto; border-top: 1px solid #f1f5f9; padding-top: 15px; }
</style>
@endsection

@section('content')
<div class="scan-page-wrapper">
    <div class="unified-card">
        
        <div class="info-badge shadow-sm">
            <small class="text-uppercase fw-bold opacity-75">Proses Pengembalian</small>
            <h4 class="fw-bold mb-0 mt-1">{{ $siswa->nama_siswa }}</h4>
            <div class="small opacity-75 fw-semibold">Wajib Kembali: <b>{{ $pinjamanAktif->chromebook->no_unit }}</b></div>
        </div>

        <div class="text-center mb-4">
            <h5 class="fw-bold text-dark">Scan Chromebook</h5>
            <p class="text-muted small">Scan unit yang sedang Anda pinjam</p>
        </div>

        <div class="scanner-view">
            <div class="laser"></div>
            <div id="reader-kembali"></div>
        </div>

        <div class="bg-light p-3 rounded-4 mt-4 d-flex align-items-center gap-3">
            <i class="bi bi-laptop text-success h4 mb-0"></i>
            <div class="small text-muted">Pastikan nomor unit sesuai dengan yang dipinjam.</div>
        </div>

        <a href="{{ route('scan.kamera') }}" class="btn btn-link text-muted text-decoration-none small mt-auto">
            <i class="bi bi-x-circle me-1"></i> Batalkan
        </a>

        <div class="system-footer text-center">
            <div class="fw-bold text-success" style="font-size: 12px; letter-spacing: 1px;">EZBORROW <span class="text-dark">SYSTEM</span></div>
            <div class="text-muted" style="font-size: 10px;">Developed by <b>Fiqri Haikal</b></div>
        </div>
    </div>
</div>

<form id="form-kembali" action="{{ route('peminjaman.kembali', $pinjamanAktif->id) }}" method="POST" class="d-none">
    @csrf
    @method('PUT')
    <input type="hidden" name="qr_chromebook_verifikasi" id="val-qr-kembali">
</form>
@endsection

@section('js')
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
    let scannerKembali = new Html5Qrcode("reader-kembali");
    
    window.onload = () => {
        scannerKembali.start({ facingMode: "environment" }, { fps: 25, qrbox: 200 }, (qr) => {
            if (navigator.vibrate) navigator.vibrate(100);
            document.getElementById('val-qr-kembali').value = qr;
            scannerKembali.stop().then(() => document.getElementById('form-kembali').submit());
        });
    };
</script>
@endsection