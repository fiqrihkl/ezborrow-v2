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
        padding: 20px; perspective: 1500px;
    }

    .unified-card {
        width: 100%; max-width: 450px;
        background: rgba(255, 255, 255, 0.85);
        backdrop-filter: blur(15px);
        -webkit-backdrop-filter: blur(15px);
        border-radius: 30px;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        position: relative; 
        border: 1px solid rgba(255, 255, 255, 0.7);
        padding: 30px;
    }

    .unified-card::after {
        content: ""; position: absolute; top: 0; left: 0; right: 0;
        height: 6px; background: linear-gradient(90deg, var(--primary-color), var(--success-color));
        border-radius: 30px 30px 0 0;
    }

    /* Area Kamera Scanner */
    .scanner-wrapper {
        position: relative; width: 100%; max-width: 280px;
        height: 280px; margin: 0 auto 25px;
        border-radius: 25px; overflow: hidden;
        background: #1a1a1a; border: 5px solid #fff;
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }
    
    #reader { width: 100% !important; height: 100% !important; }
    #reader video { object-fit: cover !important; }
    
    .scanner-overlay {
        position: absolute; inset: 0; z-index: 5;
        border: 30px solid rgba(0,0,0,0.4); pointer-events: none;
    }

    .laser-line {
        position: absolute; width: 80%; left: 10%; height: 3px;
        background: var(--primary-color);
        box-shadow: 0 0 15px var(--primary-color);
        animation: scanAnim 2s infinite linear; z-index: 7;
    }
    @keyframes scanAnim { 0%, 100% { top: 20%; opacity: 0.5; } 50% { top: 80%; opacity: 1; } }

    .instruction-box {
        background: rgba(67, 97, 238, 0.05);
        border-left: 4px solid var(--primary-color);
        padding: 12px; border-radius: 12px; margin-bottom: 20px;
    }

    .custom-input {
        border-radius: 15px !important; border: 2px solid #e2e8f0 !important;
        padding: 12px 15px !important; font-weight: 600;
    }

    .btn-save {
        background: var(--primary-color); border: none;
        border-radius: 15px; padding: 15px; font-weight: 700;
        transition: all 0.3s ease;
    }
    .btn-save:hover { background: var(--primary-light); transform: translateY(-2px); }

    .btn-switch-camera {
        position: absolute;
        bottom: 15px;
        right: 15px;
        z-index: 20;
        width: 45px;
        height: 45px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.4);
        color: white;
        display: none; /* Akan muncul via JS jika kamera > 1 */
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        transition: all 0.3s ease;
    }
    
    .btn-switch-camera:active {
        transform: scale(0.9);
        background: rgba(255, 255, 255, 0.4);
    }
</style>

<div class="scan-page-wrapper">
    <div class="unified-card">
        <div class="text-center mb-4">
            <h4 class="fw-bold text-dark mb-1">Registrasi Unit</h4>
            <p class="text-muted small">Scan QR Code untuk mendaftarkan Chromebook</p>
        </div>

        <form action="{{ route('chromebook.store') }}" method="POST">
            @csrf
            
            <div class="scanner-wrapper">
                <div id="reader"></div>
                <div class="scanner-overlay"></div>
                <div class="laser-line"></div>
                
                <button type="button" id="switchCameraBtn" class="btn-switch-camera">
                    <i class="bi bi-camera-rotate"></i>
                </button>
            </div>

            <div class="instruction-box">
                <small class="text-primary fw-bold d-block mb-1"><i class="bi bi-info-circle-fill me-1"></i> INSTRUKSI</small>
                <p class="text-dark small mb-0">Arahkan kamera ke QR Code yang tertempel di perangkat hingga terdeteksi otomatis.</p>
            </div>

            <div class="mb-3">
                <label class="form-label small fw-bold text-secondary">QR CODE UNIT</label>
                <input type="text" name="qr_code_unit" id="qr_code_unit" 
                       class="form-control custom-input bg-light" 
                       placeholder="Hasil scan akan muncul di sini..." required readonly>
            </div>

            <div class="row">
                <div class="col-6 mb-3">
                    <label class="form-label small fw-bold text-secondary">MEREK</label>
                    <input type="text" name="merek" class="form-control custom-input" placeholder="Acer/HP..." required>
                </div>
                <div class="col-6 mb-3">
                    <label class="form-label small fw-bold text-secondary">LOKER</label>
                    <input type="text" name="posisi_loker" class="form-control custom-input" placeholder="A-01...">
                </div>
            </div>

            <div class="d-grid mt-3">
                <button type="submit" class="btn btn-primary btn-save shadow-sm">
                    <i class="bi bi-cloud-check me-2"></i>SIMPAN PERANGKAT
                </button>
                <a href="{{ route('chromebook.index') }}" class="btn btn-link btn-sm text-muted mt-2 decoration-none">Batal & Kembali</a>
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
    let currentCameraId;
    let cameras = [];
    let currentFacingMode = "environment"; // Default kamera belakang

    window.addEventListener('load', function () {
        html5QrCode = new Html5Qrcode("reader");
        
        // Deteksi Kamera yang tersedia
        Html5Qrcode.getCameras().then(devices => {
            if (devices && devices.length) {
                cameras = devices;
                
                // Jika kamera lebih dari 1, munculkan tombol switch
                if (cameras.length > 1) {
                    const switchBtn = document.getElementById('switchCameraBtn');
                    switchBtn.style.display = 'flex';
                    switchBtn.addEventListener('click', toggleCamera);
                }
                
                startScanner();
            }
        }).catch(err => {
            console.error("Gagal mendeteksi kamera", err);
        });
    });

    function startScanner() {
        const config = { 
            fps: 20, 
            qrbox: { width: 200, height: 200 },
            aspectRatio: 1.0 
        };

        // Mulai dengan facingMode (lebih stabil untuk perangkat mobile)
        html5QrCode.start(
            { facingMode: currentFacingMode }, 
            config, 
            onScanSuccess
        ).catch(err => {
            console.error("Gagal memulai kamera", err);
        });
    }

    function toggleCamera() {
        if (html5QrCode.isScanning) {
            // Berhenti dulu sebelum ganti
            html5QrCode.stop().then(() => {
                // Balikkan facing mode
                currentFacingMode = (currentFacingMode === "environment") ? "user" : "environment";
                
                // Animasi putar pada tombol
                document.querySelector('#switchCameraBtn i').style.transform += 'rotate(180deg)';
                
                // Mulai lagi dengan mode baru
                startScanner();
            }).catch(err => console.error("Gagal stop kamera", err));
        }
    }

    function onScanSuccess(decodedText) {
        if (isLocked) return; 
        
        isLocked = true;
        scanAudio.play().catch(e => console.log("Audio failed"));

        document.getElementById('qr_code_unit').value = decodedText;
        const inputField = document.getElementById('qr_code_unit');
        
        // Visual Feedback
        inputField.style.backgroundColor = '#dcfce7'; // light green
        inputField.style.borderColor = '#22c55e';

        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 2000,
            timerProgressBar: true,
        });
        Toast.fire({
            icon: 'success',
            title: 'Terdeteksi: ' + decodedText
        });

        setTimeout(() => {
            isLocked = false;
            inputField.style.backgroundColor = '#f8fafc'; // back to light
            inputField.style.borderColor = '#e2e8f0';
            console.log("Scanner siap kembali...");
        }, 3000);
    }
</script>
@endsection