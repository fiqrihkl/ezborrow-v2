@extends('layouts.app')

@section('title', 'Daftar Unit Chromebook')

@section('content')
<style>
    :root { 
        --primary-color: #4361ee; 
        --primary-light: #4895ef;
        --success-color: #2ec4b6; 
        --warning-color: #f7b731;
        --danger-color: #ef4444;
    }
    
    .scan-page-wrapper {
        width: 100%; min-height: 80vh;
        display: flex; align-items: center; justify-content: center;
        padding: 20px;
    }

    .unified-card {
        width: 100%; max-width: 450px;
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(15px);
        -webkit-backdrop-filter: blur(15px);
        border-radius: 30px;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.7);
        padding: 25px;
    }

    /* Perbaikan Area Kamera Scanner */
    .scanner-container {
        position: relative;
        width: 100%;
        margin-bottom: 20px;
    }

    .scanner-wrapper {
        position: relative; 
        width: 100%; 
        aspect-ratio: 1/1; /* Memastikan frame selalu kotak */
        margin: 0 auto;
        border-radius: 20px; 
        overflow: hidden;
        background: #000; 
        border: 4px solid #fff;
        box-shadow: 0 8px 15px rgba(0,0,0,0.1);
    }
    
    /* Memaksa video html5-qrcode agar memenuhi container tanpa distorsi */
    #reader { width: 100% !important; height: 100% !important; border: none !important; }
    #reader video { 
        object-fit: cover !important; 
        width: 100% !important; 
        height: 100% !important; 
    }
    
    /* Overlay pemanis scanner */
    .scanner-overlay {
        position: absolute; inset: 0; z-index: 5;
        border: 40px solid rgba(0,0,0,0.3); pointer-events: none;
    }

    .laser-line {
        position: absolute; width: 70%; left: 15%; height: 2px;
        background: var(--primary-color);
        box-shadow: 0 0 12px var(--primary-color);
        animation: scanAnim 2s infinite ease-in-out; z-index: 7;
    }
    @keyframes scanAnim { 0%, 100% { top: 25%; opacity: 0.3; } 50% { top: 75%; opacity: 1; } }

    /* Button Switch Kamera di Bawah Frame */
    .btn-switch-camera {
        margin: 10px auto 0;
        display: none; /* Muncul via JS */
        align-items: center;
        justify-content: center;
        gap: 8px;
        background: #f1f5f9;
        border: 1px solid #e2e8f0;
        padding: 8px 16px;
        border-radius: 12px;
        color: #475569;
        font-size: 0.85rem;
        font-weight: 600;
        transition: all 0.2s;
    }
    .btn-switch-camera:active { transform: scale(0.95); background: #e2e8f0; }

    .instruction-box {
        background: rgba(67, 97, 238, 0.05);
        border-left: 4px solid var(--primary-color);
        padding: 12px; border-radius: 12px; margin-top: 15px; margin-bottom: 20px;
    }

    .custom-input {
        border-radius: 12px !important; border: 2px solid #f1f5f9 !important;
        padding: 12px 15px !important; font-weight: 600; background: #f8fafc !important;
    }

    .btn-save {
        background: var(--primary-color); border: none;
        border-radius: 15px; padding: 14px; font-weight: 700;
        transition: all 0.3s ease;
    }
    .btn-save:hover { background: var(--primary-light); transform: translateY(-2px); }

    /* Responsive Mobile */
    @media (max-width: 480px) {
        .unified-card { padding: 20px; border-radius: 20px; }
        .scanner-overlay { border-width: 25px; }
    }
</style>

<div class="scan-page-wrapper">
    <div class="unified-card">
        <div class="text-center mb-4">
            <h4 class="fw-bold text-dark mb-1">Registrasi Unit</h4>
            <p class="text-muted small">Scan QR Code pada perangkat Chromebook</p>
        </div>

        <form action="{{ route('chromebook.store') }}" method="POST">
            @csrf
            
            <div class="scanner-container">
                <div class="scanner-wrapper">
                    <div id="reader"></div>
                    <div class="scanner-overlay"></div>
                    <div class="laser-line"></div>
                </div>

                {{-- Tombol Switch diletakkan di bawah frame kamera --}}
                <button type="button" id="switchCameraBtn" class="btn-switch-camera">
                    <i class="bi bi-camera-rotate fs-5"></i>
                    <span>Ganti Kamera</span>
                </button>
            </div>

            <div class="instruction-box">
                <small class="text-primary fw-bold d-block mb-1"><i class="bi bi-info-circle-fill me-1"></i> CARA SCAN</small>
                <p class="text-dark small mb-0">Pastikan cahaya cukup dan QR Code berada di tengah kotak scanner.</p>
            </div>

            <div class="mb-3">
                <label class="form-label small fw-bold text-secondary">QR CODE UNIT</label>
                <input type="text" name="qr_code_unit" id="qr_code_unit" 
                       class="form-control custom-input" 
                       placeholder="Menunggu hasil scan..." required readonly>
            </div>

            <div class="row g-2">
                <div class="col-6 mb-3">
                    <label class="form-label small fw-bold text-secondary">MEREK</label>
                    <input type="text" name="merek" class="form-control custom-input" placeholder="Zyrex, Evercoss ..." required>
                </div>
                <div class="col-6 mb-3">
                    <label class="form-label small fw-bold text-secondary">LOKER</label>
                    <input type="text" name="posisi_loker" class="form-control custom-input" placeholder="01">
                </div>
            </div>

            <div class="d-grid mt-3">
                <button type="submit" class="btn btn-primary btn-save shadow-sm">
                    <i class="bi bi-plus-circle me-2"></i>SIMPAN PERANGKAT
                </button>
                <a href="{{ route('chromebook.index') }}" class="btn btn-link btn-sm text-muted mt-2 text-decoration-none">Kembali</a>
            </div>
        </form>
    </div>
</div>

<audio id="scanAudio">
    <source src="{{ asset('assets/audio/scan.mp3') }}" type="audio/mpeg">
</audio>
@endsection

@section('js')
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    let html5QrCode;
    const scanAudio = document.getElementById('scanAudio');
    let isLocked = false;
    let currentFacingMode = "environment"; 

    window.addEventListener('load', function () {
        html5QrCode = new Html5Qrcode("reader");
        
        // Cek jumlah kamera
        Html5Qrcode.getCameras().then(devices => {
            if (devices && devices.length > 1) {
                const switchBtn = document.getElementById('switchCameraBtn');
                switchBtn.style.display = 'flex';
                switchBtn.addEventListener('click', toggleCamera);
            }
            startScanner();
        }).catch(err => {
            console.error("Gagal deteksi kamera", err);
            startScanner(); // Coba running saja meski deteksi gagal
        });
    });

    function startScanner() {
        // Pengaturan config yang lebih fleksibel untuk mobile
        const config = { 
            fps: 15, 
            qrbox: (viewfinderWidth, viewfinderHeight) => {
                const minEdge = Math.min(viewfinderWidth, viewfinderHeight);
                const qrboxSize = Math.floor(minEdge * 0.6);
                return { width: qrboxSize, height: qrboxSize };
            },
            // Menghapus paksaan aspect ratio agar mengikuti container CSS
            videoConstraints: {
                facingMode: currentFacingMode
            }
        };

        html5QrCode.start(
            { facingMode: currentFacingMode }, 
            config, 
            onScanSuccess
        ).catch(err => {
            console.error("Kamera gagal dimulai", err);
        });
    }

    function toggleCamera() {
        if (html5QrCode.isScanning) {
            html5QrCode.stop().then(() => {
                currentFacingMode = (currentFacingMode === "environment") ? "user" : "environment";
                
                // Efek rotasi icon
                const icon = document.querySelector('#switchCameraBtn i');
                icon.style.transition = 'transform 0.4s';
                icon.style.transform = (icon.style.transform === 'rotate(180deg)') ? 'rotate(0deg)' : 'rotate(180deg)';
                
                startScanner();
            }).catch(err => console.error("Gagal pindah kamera", err));
        }
    }

    function onScanSuccess(decodedText) {
        if (isLocked) return; 
        
        isLocked = true;
        
        // Audio feedback
        if (scanAudio) {
            scanAudio.play().catch(() => {});
        }

        const inputField = document.getElementById('qr_code_unit');
        inputField.value = decodedText;
        
        // Haptic/Visual Feedback
        inputField.classList.add('is-valid');
        inputField.style.background = '#e8f5e9 !important';

        Swal.fire({
            icon: 'success',
            title: 'QR Code Terdeteksi',
            text: decodedText,
            timer: 1500,
            showConfirmButton: false,
            toast: true,
            position: 'top-end'
        });

        // Kunci scanner sebentar agar tidak scan berkali-kali dalam 1 detik
        setTimeout(() => {
            isLocked = false;
        }, 2000);
    }
</script>
@endsection