@extends('layouts.app')

@section('title', 'Konfirmasi & Scan Perangkat')

@section('css')
<style>
    :root { --primary-color: #0d6efd; --soft-bg: #f8fafc; }

    .scan-page-wrapper {
        position: fixed; top: 0; left: 0; width: 100vw; height: 100vh;
        display: flex; align-items: center; justify-content: center;
        background: radial-gradient(circle at center, rgba(13, 110, 253, 0.05) 0%, var(--soft-bg) 100%);
        perspective: 1000px; /* Penting untuk efek 3D */
    }

    /* Struktur Flip Card */
    .flip-card {
        width: 400px; height: 580px; /* Ukuran Konsisten */
        transition: transform 0.8s cubic-bezier(0.4, 0, 0.2, 1);
        transform-style: preserve-3d;
    }

    .flip-card.is-flipped { transform: rotateY(180deg); }

    .card-face {
        position: absolute; width: 100%; height: 100%;
        backface-visibility: hidden;
        background: white; border-radius: 45px;
        padding: 35px 30px;
        box-shadow: 0 25px 80px rgba(0, 0, 0, 0.08);
        border: 1px solid rgba(13, 110, 253, 0.05);
        display: flex; flex-direction: column;
    }

    .card-back { transform: rotateY(180deg); }

    /* Stepper Konsisten */
    .stepper-container { margin-bottom: 25px; }
    .progress-track { height: 4px; background: #e9ecef; border-radius: 10px; position: relative; margin-top: 15px; }
    .progress-bar-fill { height: 100%; background: var(--primary-color); transition: width 0.6s ease; }
    .stepper-list { display: flex; justify-content: space-between; list-style: none; padding: 0; margin-top: -15px; }
    .step-circle { width: 30px; height: 30px; background: white; border: 2px solid #dee2e6; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 12px; z-index: 5; }
    .step-node.active .step-circle { background: var(--primary-color); color: white; border-color: var(--primary-color); }
    .step-node.completed .step-circle { background: #198754; color: white; border-color: #198754; }

    /* Form Styles */
    .info-badge { background: #0d6efd; color: white; border-radius: 20px; padding: 20px; margin-bottom: 20px; }
    .custom-select { border-radius: 15px !important; padding: 12px !important; border: 2px solid #f1f5f9; font-weight: 600; }
    
    /* Scanner Styles */
    .scanner-view { position: relative; width: 220px; height: 220px; margin: 20px auto; border-radius: 30px; overflow: hidden; background: #000; border: 4px solid #fff; box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
    .laser { position: absolute; width: 100%; height: 3px; background: var(--primary-color); box-shadow: 0 0 15px var(--primary-color); animation: laserAnim 3s infinite ease-in-out; z-index: 10; }
    @keyframes laserAnim { 0%, 100% { top: 10%; } 50% { top: 90%; } }

    #reader { width: 100% !important; height: 100% !important; }
    video { object-fit: cover !important; }

    .system-footer { margin-top: auto; border-top: 1px solid #f1f5f9; padding-top: 15px; font-size: 10px; }
</style>
@endsection

@section('content')
<div class="scan-page-wrapper">
    <div class="flip-card" id="mainFlipCard">
        
        <div class="card-face card-front">
            <div class="stepper-container">
                <div class="progress-track"><div class="progress-bar-fill" style="width: 50%;"></div></div>
                <ul class="stepper-list">
                    <li class="step-node completed"><div class="step-circle"><i class="bi bi-check"></i></div></li>
                    <li class="step-node active"><div class="step-circle">2</div></li>
                    <li class="step-node"><div class="step-circle">3</div></li>
                </ul>
            </div>

            <div class="info-badge">
                <small class="text-uppercase fw-bold opacity-75">Siswa Peminjam</small>
                <h4 class="fw-bold mb-0">{{ $siswa->nama_siswa }}</h4>
                <div class="small fw-semibold mt-1">{{ $siswa->kelas->nama_kelas }}</div>
            </div>

            <div class="text-start">
                <label class="small fw-bold text-muted mb-2 ms-2">PILIH GURU MENGAJAR:</label>
                <select id="guru_id" class="form-select custom-select shadow-sm mb-3">
                    <option value="" selected disabled>-- Pilih Guru --</option>
                    @foreach($gurus as $guru)
                        <option value="{{ $guru->id }}">{{ $guru->nama_guru }}</option>
                    @endforeach
                </select>

                <label class="small fw-bold text-muted mb-2 ms-2">MATA PELAJARAN:</label>
                <select id="mapel_id" class="form-select custom-select shadow-sm" disabled>
                    <option value="" selected disabled>-- Pilih Guru Dulu --</option>
                </select>
            </div>

            <div class="mt-4 alert alert-info py-2 rounded-4 small border-0">
                <i class="bi bi-info-circle-fill me-2"></i> Pilih Mapel untuk lanjut ke scan unit.
            </div>

            <div class="system-footer text-center">
                <div class="fw-bold text-primary">EZBORROW <span class="text-dark">SYSTEM</span></div>
                <div class="text-muted">Developed by Fiqri Haikal</div>
            </div>
        </div>

        <div class="card-face card-back">
            <div class="stepper-container">
                <div class="progress-track"><div class="progress-back-fill" style="width: 100%; height: 4px; background: var(--primary-color); border-radius: 10px;"></div></div>
                <ul class="stepper-list">
                    <li class="step-node completed"><div class="step-circle"><i class="bi bi-check"></i></div></li>
                    <li class="step-node completed"><div class="step-circle"><i class="bi bi-check"></i></div></li>
                    <li class="step-node active"><div class="step-circle">3</div></li>
                </ul>
            </div>

            <div class="text-center mb-2">
                <div class="badge bg-primary bg-opacity-10 text-primary px-4 py-2 rounded-pill fw-bold">
                    <i class="bi bi-qr-code-scan me-2"></i> PINDAI CHROMEBOOK
                </div>
            </div>

            <div class="scanner-view">
                <div class="laser"></div>
                <div id="reader"></div>
            </div>

            <div class="bg-light p-3 rounded-4 mb-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="h3 mb-0 text-primary"><i class="bi bi-laptop"></i></div>
                    <div class="text-start">
                        <div class="small fw-bold text-dark">Langkah Terakhir</div>
                        <div class="small text-muted" style="font-size: 11px;">Arahkan kamera ke QR Code di body Chromebook.</div>
                    </div>
                </div>
            </div>

            <button onclick="flipBack()" class="btn btn-link text-muted text-decoration-none small fw-bold">
                <i class="bi bi-arrow-left"></i> Kembali ke Pilih Guru
            </button>

            <div class="system-footer text-center mt-auto">
                <div class="fw-bold text-primary">EZBORROW <span class="text-dark">SYSTEM</span></div>
                <div class="text-muted">Developed by Fiqri Haikal</div>
            </div>
        </div>

    </div>
</div>

<form id="finalForm" action="{{ route('peminjaman.final') }}" method="POST" class="d-none">
    @csrf
    <input type="hidden" name="siswa_id" value="{{ $siswa->id }}">
    <input type="hidden" name="guru_id" id="final_guru_id">
    <input type="hidden" name="mapel_id" id="final_mapel_id">
    <input type="hidden" name="qr_chromebook" id="final_qr_chromebook">
</form>
@endsection

@section('js')
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
    const flipCard = document.getElementById('mainFlipCard');
    const guruSelect = document.getElementById('guru_id');
    const mapelSelect = document.getElementById('mapel_id');
    let html5QrCode;

    // AJAX Mapel
    guruSelect.addEventListener('change', function() {
        fetch(`/get-mapel-by-guru/${this.value}`)
            .then(res => res.json())
            .then(data => {
                mapelSelect.innerHTML = '<option value="" selected disabled>-- Pilih Mapel --</option>';
                data.forEach(m => mapelSelect.innerHTML += `<option value="${m.id}">${m.nama_mapel}</option>`);
                mapelSelect.disabled = false;
            });
    });

    // Flip to Back (Scan Section)
    mapelSelect.addEventListener('change', function() {
        if(this.value) {
            flipCard.classList.add('is-flipped');
            setTimeout(startScanner, 800);
        }
    });

    function flipBack() {
        if(html5QrCode) html5QrCode.stop();
        flipCard.classList.remove('is-flipped');
    }

    function startScanner() {
        html5QrCode = new Html5Qrcode("reader");
        html5QrCode.start(
            { facingMode: "environment" }, 
            { fps: 25, qrbox: 200 },
            (decodedText) => {
                if (navigator.vibrate) navigator.vibrate(100);
                document.getElementById('final_guru_id').value = guruSelect.value;
                document.getElementById('final_mapel_id').value = mapelSelect.value;
                document.getElementById('final_qr_chromebook').value = decodedText;
                
                html5QrCode.stop().then(() => {
                    document.getElementById('finalForm').submit();
                });
            }
        ).catch(err => console.error("Kamera Error:", err));
    }
</script>
@endsection