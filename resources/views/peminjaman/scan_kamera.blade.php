@extends('layouts.app')

@section('title', 'Smart Peminjaman & Pengembalian')

@section('css')
<style>
    :root { 
        --primary-color: #4361ee; 
        --primary-light: #4895ef;
        --success-color: #2ec4b6; 
        --warning-color: #f7b731;
        --danger-color: #ef4444;
        --bg-slate: #f1f5f9;
    }
    
    body { 
        background-color: #e2e8f0;
        background-image: 
            radial-gradient(at 0% 0%, rgba(67, 97, 238, 0.15) 0px, transparent 50%),
            radial-gradient(at 100% 100%, rgba(46, 196, 182, 0.15) 0px, transparent 50%);
        margin: 0; 
        padding: 0;
        height: 100vh;
        width: 100vw;
        overflow: hidden;
        font-family: 'Inter', 'Segoe UI', sans-serif; 
        display: flex;
        align-items: center;
        justify-content: center;
    }

    body::before {
        content: "";
        position: absolute;
        top: 0; left: 0; width: 100%; height: 100%;
        background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%234361ee' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        z-index: -1;
    }

    .scan-page-wrapper {
        width: 100%; 
        height: 100%;
        display: flex; 
        align-items: center; 
        justify-content: center;
        padding: 10px;
        perspective: 1500px; /* Menambah kedalaman 3D agar lebih dramatis */
    }

    .unified-card {
        width: 100%; 
        max-width: 400px; 
        height: 96vh;
        max-height: 680px; 
        background: rgba(255, 255, 255, 0.85);
        backdrop-filter: blur(15px);
        -webkit-backdrop-filter: blur(15px);
        border-radius: 30px;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        position: relative; 
        border: 1px solid rgba(255, 255, 255, 0.7);
        transform-style: preserve-3d;
    }

    /* Container yang akan berputar */
    .flip-inner {
        width: 100%;
        height: 100%;
        padding: 20px;
        display: flex;
        flex-direction: column;
        /* Durasi dipercepat sedikit agar terasa responsif */
        transition: transform 0.6s cubic-bezier(0.4, 0, 0.2, 1);
        transform-style: preserve-3d;
        will-change: transform; /* Optimasi performa browser */
    }

    /* PERBAIKAN: Menggunakan 180deg agar hanya berputar setengah (membalik) */
    .is-flipping {
        transform: rotateY(180deg);
    }

    .unified-card::after {
        content: "";
        position: absolute;
        top: 0; left: 0; right: 0;
        height: 6px;
        background: linear-gradient(90deg, var(--primary-color), var(--success-color));
        z-index: 10;
        border-radius: 30px 30px 0 0;
    }

    /* Stepper Modern */
    .stepper { 
        display: flex; justify-content: space-between; 
        margin-bottom: 20px; position: relative; 
        flex-shrink: 0; padding: 0 10px;
        /* Menjaga stepper tetap menghadap depan saat di-flip */
        backface-visibility: hidden; 
    }
    .progress-line { 
        position: absolute; top: 50%; left: 0; transform: translateY(-50%);
        width: 100%; height: 6px; background: #e2e8f0; z-index: 1; 
        border-radius: 10px;
    }
    .progress-line-fill { 
        position: absolute; height: 100%; 
        background: linear-gradient(90deg, var(--primary-color), var(--primary-light));
        transition: width 0.5s ease; z-index: 1; border-radius: 10px;
    }
    .step-dot { 
        width: 32px; height: 32px; border-radius: 50%; 
        background: white; border: 3px solid #cbd5e1; 
        display: flex; align-items: center; justify-content: center; 
        font-size: 12px; font-weight: 800; z-index: 2; color: #94a3b8; 
        transition: all 0.3s ease;
    }
    .step-dot.active { 
        border-color: var(--primary-color); color: var(--primary-color); 
        transform: scale(1.1); box-shadow: 0 0 10px rgba(67, 97, 238, 0.2);
    }
    .step-dot.done { background: var(--primary-color); border-color: var(--primary-color); color: white; }

    /* Content Area */
    .step-content { 
        display: none; flex-direction: column; flex: 1; 
        justify-content: space-evenly; min-height: 0;
        /* Mencegah konten terlihat terbalik saat di-flip */
        backface-visibility: hidden; 
    }
    .step-content.active { display: flex; }

    /* Kamera Scanner */
    .scanner-container {
        position: relative; width: 210px; height: 210px; 
        margin: 0 auto; border-radius: 20px;
        overflow: hidden; background: #1a1a1a;
        border: 4px solid #fff; box-shadow: 0 10px 20px rgba(0,0,0,0.15);
        flex-shrink: 1;
    }
    .scanner-container video { width: 100% !important; height: 100% !important; object-fit: cover !important; }
    .scanner-overlay { position: absolute; inset: 0; z-index: 5; border: 20px solid rgba(0,0,0,0.4); pointer-events: none; }
    .laser-line {
        position: absolute; width: 80%; left: 10%; height: 2px;
        background: var(--primary-color); box-shadow: 0 0 10px var(--primary-color);
        animation: scanAnim 2s infinite linear; z-index: 7;
    }
    @keyframes scanAnim { 0%, 100% { top: 20%; opacity: 0.5; } 50% { top: 80%; opacity: 1; } }

    /* Form & UI */
    .instruction-box { background: rgba(67, 97, 238, 0.05); border-left: 4px solid var(--primary-color); padding: 8px 12px; border-radius: 10px; margin: 5px 0; }
    .custom-select { height: 46px !important; border-radius: 12px !important; font-weight: 600; border: 2px solid #e2e8f0 !important; margin-bottom: 8px; font-size: 13px; background-color: #f8fafc !important; }
    .btn-action { height: 48px; border-radius: 12px; font-weight: 700; letter-spacing: 0.5px; }
    .system-footer { margin-top: 10px; padding-top: 8px; border-top: 1px dashed #e2e8f0; backface-visibility: hidden; }
    .footer-text { font-size: 8px; letter-spacing: 1px; color: #94a3b8; text-transform: uppercase; }

    @media (max-height: 600px) {
        .scanner-container { width: 170px; height: 170px; }
        .unified-card { padding: 15px; }
    }
</style>
@endsection

@section('content')
<div class="scan-page-wrapper">
    <div class="unified-card" id="main-card">
        <div class="flip-inner" id="flip-content">
            
            {{-- Stepper Progress --}}
            <div class="stepper" id="main-stepper">
                <div class="progress-line"><div class="progress-line-fill" id="p-fill" style="width: 0%;"></div></div>
                <div class="step-dot active" id="dot-1">1</div>
                <div class="step-dot" id="dot-2">2</div>
                <div class="step-dot" id="dot-3">3</div>
            </div>

            {{-- Step 1: Scan Kartu Pelajar --}}
            <div id="step-1" class="step-content active">
                <div class="text-center">
                    <span class="badge bg-primary px-3 py-2 rounded-pill mb-2">TAHAP SCAN ID</span>
                    <h5 class="fw-bold text-dark mb-1">Kartu Pelajar</h5>
                    <p class="text-muted small">Arahkan QR Code ke area scanner</p>
                </div>
                <div class="scanner-container">
                    <div class="scanner-overlay"></div>
                    <div class="laser-line"></div>
                    <div id="reader-siswa"></div>
                </div>
                <div class="instruction-box">
                    <small class="text-secondary"><i class="fas fa-info-circle text-primary me-2"></i>Sistem otomatis mendeteksi Pinjam/Kembali.</small>
                </div>
            </div>

            {{-- Step 2: Form Guru & Mapel --}}
            <div id="step-2" class="step-content">
                <div class="text-center mb-3">
                    <div class="avatar-placeholder mx-auto mb-2" style="width: 60px; height: 60px; background: var(--primary-color); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white;">
                        <i class="fas fa-user-check fa-lg"></i>
                    </div>
                    <h5 id="display-nama" class="fw-bold text-dark mb-0">...</h5>
                    <p id="display-kelas" class="badge bg-light text-primary border border-primary-subtle">Kelas: -</p>
                </div>
                <div class="form-body flex-grow-1">
                    <label class="small fw-bold text-secondary mb-1 ms-1">GURU PENGAMPU</label>
                    <select id="guru_id_select" class="form-select custom-select">
                        <option value="" selected disabled>Pilih Guru...</option>
                        @foreach($gurus as $guru)
                            <option value="{{ $guru->id }}">{{ $guru->nama_guru }}</option>
                        @endforeach
                    </select>
                    <label class="small fw-bold text-secondary mb-1 ms-1">MATA PELAJARAN</label>
                    <select id="mapel_id_select" class="form-select custom-select" disabled>
                        <option value="" selected disabled>Pilih guru dahulu</option>
                    </select>
                </div>
                <button onclick="window.location.reload()" class="btn btn-link text-decoration-none text-muted fw-bold">BATALKAN SESI</button>
            </div>

            {{-- Step 3: Scan Perangkat (Chromebook) --}}
            <div id="step-3" class="step-content">
                <div class="text-center">
                    <span class="badge bg-warning text-dark px-3 py-2 rounded-pill mb-2">TAHAP AKHIR</span>
                    <h5 class="fw-bold text-dark mb-1">Scan QR Chromebook</h5>
                    <p class="text-muted small">Scan stiker QR di perangkat</p>
                </div>
                <div class="scanner-container" style="border-color: var(--warning-color);">
                    <div class="scanner-overlay"></div>
                    <div class="laser-line" style="background: var(--warning-color); box-shadow: 0 0 15px var(--warning-color);"></div>
                    <div id="reader-unit"></div>
                </div>
                <div class="alert alert-warning border-0 shadow-sm py-2 px-3 rounded-4 mb-0 text-center">
                    <small class="fw-bold" style="font-size: 11px;"><i class="fas fa-exclamation-triangle me-1"></i> Pastikan nomor unit sesuai!</small>
                </div>
            </div>

            {{-- Step Khusus Pengembalian --}}
            <div id="step-kembali" class="step-content">
                <div class="text-center">
                    <div class="badge bg-success px-4 py-2 rounded-pill mb-2">MODE PENGEMBALIAN</div>
                    <h5 id="kembali-nama" class="fw-bold text-dark mb-0">-</h5>
                    <div class="mt-2"><span class="badge bg-dark rounded-3 px-3" id="kembali-unit">-</span></div>
                </div>
                <div class="scanner-container" style="border-color: var(--success-color);">
                    <div class="scanner-overlay"></div>
                    <div class="laser-line" style="background:var(--success-color); box-shadow: 0 0 15px var(--success-color);"></div>
                    <div id="reader-kembali"></div>
                </div>
                <button onclick="window.location.reload()" class="btn btn-outline-danger btn-action w-100 border-2">BATALKAN</button>
            </div>

            <div class="system-footer text-center">
                <div class="footer-text">
                    <strong>EZBorrow v2.0</strong> • Digital Library<br>
                    <span class="text-primary fw-bolder">Developed by Fiqri Haikal</span>
                </div>
            </div>
            
        </div>
    </div>
</div>

{{-- Audio Elements (Lokal dari public/assets/audio/) --}}
<audio id="audioScan" src="{{ asset('assets/audio/scan.mp3') }}" preload="auto"></audio>
<audio id="audioSuccess" src="{{ asset('assets/audio/success.mp3') }}" preload="auto"></audio>
<audio id="audioError" src="{{ asset('assets/audio/error.mp3') }}" preload="auto"></audio>

{{-- Form Hidden untuk Peminjaman --}}
<form id="finalForm" action="{{ route('peminjaman.final') }}" method="POST" class="d-none">
    @csrf
    <input type="hidden" name="siswa_id" id="val-siswa">
    <input type="hidden" name="guru_id" id="val-guru">
    <input type="hidden" name="mapel_id" id="val-mapel">
    <input type="hidden" name="qr_chromebook" id="val-qr">
</form>

{{-- Form Hidden untuk Pengembalian --}}
<form id="form-kembali" action="" method="POST" class="d-none">
    @csrf @method('PUT')
    <input type="hidden" name="qr_chromebook_verifikasi" id="val-qr-kembali">
</form>
@endsection

@section('js')
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    let scannerSiswa, scannerUnit, scannerKembali;
    let isProcessing = false;
    const qrConfig = { fps: 25, qrbox: { width: 180, height: 180 }, aspectRatio: 1.0 };

    // --- FUNGSI AUDIO ---
    function playAudio(id) {
        const audio = document.getElementById(id);
        if (audio) {
            audio.currentTime = 0; // Reset ke awal jika suara masih main
            audio.play().catch(e => console.log("Audio play blocked: ", e));
        }
    }

    window.onload = () => {
        startScannerSiswa();
        checkFlashMessages();
    };

    // --- SCANNER SISWA (STEP 1) ---
    function startScannerSiswa() {
        isProcessing = false;
        if (scannerSiswa) scannerSiswa.clear();
        scannerSiswa = new Html5Qrcode("reader-siswa");
        scannerSiswa.start({ facingMode: "environment" }, qrConfig, (qr) => {
            if (isProcessing) return;
            playAudio('audioScan'); // Suara saat scan terdeteksi
            processSiswaScan(qr);
        }).catch(err => console.error(err));
    }

    function processSiswaScan(qr) {
        isProcessing = true;
        fetch(`/get-siswa-by-qr/${qr}`)
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    scannerSiswa.stop().then(() => {
                        if(data.mode === 'kembali') {
                            document.getElementById('kembali-nama').innerText = data.siswa.nama_siswa;
                            document.getElementById('kembali-unit').innerText = "UNIT: " + data.siswa.no_unit;
                            document.getElementById('form-kembali').action = `/peminjaman/kembali/${data.pinjaman_id}`;
                            goToStep('kembali');
                        } else {
                            document.getElementById('display-nama').innerText = data.siswa.nama_siswa;
                            document.getElementById('display-kelas').innerText = "Kelas: " + data.siswa.nama_kelas;
                            document.getElementById('val-siswa').value = data.siswa.id;
                            goToStep(2);
                        }
                    });
                } else {
                    playAudio('audioError'); // Suara saat scan gagal (Siswa tidak terdaftar)
                    Swal.fire({
                        icon: 'error',
                        title: 'AKSES DITOLAK',
                        text: data.message,
                        timer: 3000,
                        showConfirmButton: false,
                        willClose: () => { isProcessing = false; }
                    });
                }
            });
    }

    // --- HANDLING GURU & MAPEL (STEP 2) ---
    document.getElementById('guru_id_select').addEventListener('change', function() {
        const mSelect = document.getElementById('mapel_id_select');
        mSelect.disabled = true;
        fetch(`/get-mapel-by-guru/${this.value}`)
            .then(res => res.json())
            .then(data => {
                let opt = '<option value="" disabled selected>-- Pilih Mapel --</option>';
                data.forEach(m => opt += `<option value="${m.id}">${m.nama_mapel}</option>`);
                mSelect.innerHTML = opt;
                mSelect.disabled = false;
            });
    });

    document.getElementById('mapel_id_select').addEventListener('change', () => {
        setTimeout(() => goToStep(3), 400);
    });

    // --- SCANNER UNIT CHROMEBOOK (STEP 3) ---
    function startScannerUnit() {
        isProcessing = false;
        document.getElementById('val-guru').value = document.getElementById('guru_id_select').value;
        document.getElementById('val-mapel').value = document.getElementById('mapel_id_select').value;
        if (scannerUnit) scannerUnit.clear();
        scannerUnit = new Html5Qrcode("reader-unit");
        scannerUnit.start({ facingMode: "environment" }, qrConfig, (qr) => {
            if (isProcessing) return;
            isProcessing = true;
            playAudio('audioScan'); // Suara saat scan unit
            document.getElementById('val-qr').value = qr;
            scannerUnit.stop().then(() => {
                Swal.fire({
                    title: 'Memvalidasi...',
                    showConfirmButton: false,
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });
                document.getElementById('finalForm').submit();
            });
        });
    }

    // --- SCANNER VERIFIKASI UNIT (PENGEMBALIAN) ---
    function startScannerKembali() {
        isProcessing = false;
        if (scannerKembali) scannerKembali.clear();
        scannerKembali = new Html5Qrcode("reader-kembali");
        scannerKembali.start({ facingMode: "environment" }, qrConfig, (qr) => {
            if (isProcessing) return;
            isProcessing = true;
            playAudio('audioScan'); // Suara saat scan verifikasi kembali
            document.getElementById('val-qr-kembali').value = qr;
            scannerKembali.stop().then(() => {
                Swal.fire({ title: 'Memproses...', showConfirmButton: false, didOpen: () => Swal.showLoading() });
                document.getElementById('form-kembali').submit();
            });
        });
    }

    // --- NAVIGATION LOGIC ---
    function goToStep(step) {
        const flipContent = document.getElementById('flip-content');
        flipContent.classList.add('is-flipping');
        setTimeout(() => {
            document.querySelectorAll('.step-content').forEach(s => s.classList.remove('active'));
            const stepper = document.getElementById('main-stepper');
            
            if (step === 'kembali') {
                stepper.style.display = 'none';
                document.getElementById('step-kembali').classList.add('active');
                startScannerKembali();
            } else {
                stepper.style.display = 'flex';
                document.getElementById(`step-${step}`).classList.add('active');
                const fill = document.getElementById('p-fill');
                fill.style.width = step === 1 ? '0%' : (step === 2 ? '50%' : '100%');
                [1, 2, 3].forEach(d => {
                    const dot = document.getElementById(`dot-${d}`);
                    if(d < step) { 
                        dot.className = 'step-dot done'; 
                        dot.innerHTML = '<i class="fas fa-check"></i>'; 
                    } else if(d === step) { 
                        dot.className = 'step-dot active'; 
                        dot.innerHTML = d; 
                    } else { 
                        dot.className = 'step-dot'; 
                        dot.innerHTML = d; 
                    }
                });
                if(step === 3) startScannerUnit();
            }
        }, 300);
        setTimeout(() => { flipContent.classList.remove('is-flipping'); }, 600);
    }

    // --- FLASH MESSAGES & SOUNDS ---
    function checkFlashMessages() {
        // KONDISI BERHASIL (PINJAM DENGAN VOUCHER ATAU KEMBALI BIASA)
        @if(session('voucher_baru') || session('success'))
            playAudio('audioSuccess'); 
            
            @if(session('voucher_baru'))
                Swal.fire({
                    title: `<div style="font-weight: 800; color: #1e293b; margin-top: 10px;">BERHASIL DIPINJAM</div>`,
                    html: `
                        <div style="padding: 0 5px; font-family: 'Inter', sans-serif;">
                            <p style="color: #64748b; font-size: 14px; margin-bottom: 20px;">Silakan catat kode voucher WiFi Anda:</p>
                            <div style="background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
                                        border-radius: 20px; padding: 30px 15px; color: white; 
                                        box-shadow: 0 15px 30px rgba(0,0,0,0.15); position: relative; overflow: hidden; margin-bottom: 20px;">
                                <h2 style="font-family: 'Monaco', monospace; font-weight: 800; letter-spacing: 6px; margin: 0; font-size: 2.5rem;">
                                    {{ session('voucher_baru') }}
                                </h2>
                                <div style="margin-top: 15px; font-size: 10px; color: #94a3b8;">
                                    Expired: {{ date('H:i', strtotime('+2 hours')) }} WITA
                                </div>
                            </div>
                            <div style="background: #fffbeb; border: 1px solid #fef3c7; border-radius: 12px; padding: 12px; display: flex; gap: 10px; align-items: center;">
                                <i class="fas fa-exclamation-triangle" style="color: #f59e0b;"></i>
                                <span style="font-size: 11px; color: #92400e; text-align: left;">JANGAN TUTUP sebelum mencatat. Kode hanya muncul sekali.</span>
                            </div>
                        </div>
                    `,
                    confirmButtonText: 'SAYA SUDAH CATAT / SELESAI',
                    confirmButtonColor: '#1e293b',
                    allowOutsideClick: false,
                    width: '400px',
                    customClass: { popup: 'rounded-5' }
                });
            @else
                Swal.fire({
                    icon: 'success',
                    title: 'SUKSES',
                    text: '{{ session("success") }}',
                    timer: 5000,
                    showConfirmButton: false,
                    customClass: { popup: 'rounded-4' }
                });
            @endif
        @endif

        // KONDISI GAGAL
        @if(session('error'))
            playAudio('audioError'); 
            Swal.fire({
                icon: 'error',
                title: 'GAGAL',
                text: '{{ session("error") }}',
                timer: 5000,
                showConfirmButton: false,
                customClass: { popup: 'rounded-4' }
            });
        @endif
    }
</script>
@endsection