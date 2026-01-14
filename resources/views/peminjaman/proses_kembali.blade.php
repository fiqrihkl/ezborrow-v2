@extends('layouts.app')

@section('title', 'Konfirmasi Pengembalian')

@section('css')
<style>
    /* Mengatur container agar semua elemen di tengah layar */
    .full-center-wrapper {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        min-height: 85vh;
        width: 100%;
        text-align: center;
    }

    /* Bingkai Kamera 1x1 */
    .scanner-box {
        width: 300px;
        height: 300px;
        background: #000;
        border-radius: 25px;
        overflow: hidden;
        border: 5px solid #198754; /* Warna hijau untuk pengembalian */
        position: relative;
        box-shadow: 0 15px 35px rgba(25, 135, 84, 0.2);
        margin: 20px auto;
    }

    #reader {
        width: 100% !important;
        height: 100% !important;
        border: none !important;
    }

    #reader video {
        width: 100% !important;
        height: 100% !important;
        object-fit: cover !important;
    }

    /* Sembunyikan elemen sampah library */
    #reader__header, #reader__status_span, #reader__dashboard_section_csr a {
        display: none !important;
    }

    .waiting-scan { animation: pulse 1.5s infinite; }
    @keyframes pulse { 0% { opacity: 1; } 50% { opacity: 0.5; } 100% { opacity: 1; } }
</style>
@endsection

@section('content')
<div class="container">
    <div class="full-center-wrapper">
        
        <div class="mb-3">
            <h2 class="fw-bold text-success">Konfirmasi Pengembalian</h2>
            <h4 class="mb-0">{{ $siswa->nama_siswa }}</h4>
            <p class="text-muted">{{ $siswa->kelas->nama_kelas ?? 'Kelas tidak terdaftar' }}</p>
        </div>

        <div class="card border-0 shadow-sm mb-4" style="max-width: 400px; width: 100%; border-radius: 15px;">
            <div class="card-body bg-light rounded-3">
                <small class="text-uppercase fw-bold text-secondary d-block mb-1">Unit Chromebook:</small>
                <h3 class="text-dark fw-bold mb-0">{{ $pinjamanAktif->chromebook->no_unit }}</h3>
                <span class="badge bg-success px-3">Loker: {{ $pinjamanAktif->chromebook->loker }}</span>
            </div>
        </div>

        <div id="statusLabel" class="mb-2">
            <span class="badge bg-danger p-2 waiting-scan">🔴 SIAP PINDAI UNIT...</span>
        </div>

        <form id="returnForm" action="{{ route('peminjaman.kembali', $pinjamanAktif->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            @if(session('metode_scan') == 'kamera')
                <div class="scanner-box">
                    <div id="reader"></div>
                </div>
                <input type="hidden" name="qr_chromebook_verifikasi" id="qr_verifikasi">
            @else
                <div style="max-width: 400px; margin: 0 auto;">
                    <input type="text" name="qr_chromebook_verifikasi" id="qr_verifikasi" 
                           class="form-control form-control-lg text-center border-success shadow-sm" 
                           placeholder="Tembak QR Chromebook..." required autofocus autocomplete="off">
                </div>
            @endif
        </form>

        <div class="mt-4">
            <a href="{{ route(session('metode_scan') == 'kamera' ? 'scan.kamera' : 'scan.manual') }}" 
               class="text-decoration-none text-muted small">← Batal & Kembali</a>
        </div>

    </div>
</div>
@endsection

@section('js')
@if(session('metode_scan') == 'kamera')
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
@endif

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const qrInput = document.getElementById('qr_verifikasi');
        const returnForm = document.getElementById('returnForm');
        const statusLabel = document.getElementById('statusLabel');
        let isProcessing = false;

        @if(session('metode_scan') == 'kamera')
            // LOGIKA SCAN KAMERA
            let html5QrcodeScanner = new Html5QrcodeScanner(
                "reader", { fps: 20, qrbox: 200, aspectRatio: 1.0 }, false
            );

            html5QrcodeScanner.render((decodedText) => {
                if (isProcessing) return;
                
                isProcessing = true;
                qrInput.value = decodedText;
                
                // Efek Berhasil
                html5QrcodeScanner.pause(true);
                document.querySelector('.scanner-box').style.borderColor = "#ffc107";
                statusLabel.innerHTML = '<span class="badge bg-warning text-dark p-2">⌛ VERIFIKASI UNIT...</span>';
                
                setTimeout(() => { returnForm.submit(); }, 600);
            });
        @else
            // LOGIKA SCAN LASER
            qrInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    if (isProcessing) return;
                    
                    if(this.value.trim() !== "") {
                        isProcessing = true;
                        statusLabel.innerHTML = '<span class="badge bg-warning text-dark p-2">⌛ VERIFIKASI UNIT...</span>';
                        returnForm.submit();
                    }
                }
            });

            // Jaga Fokus Input Manual
            document.addEventListener('click', () => {
                qrInput.focus();
            });
        @endif
    });
</script>
@endsection