@extends('layouts.app')

@section('title', 'Tambah Unit Chromebook')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="d-flex align-items-center mb-4">
                <a href="{{ route('chromebook.index') }}" class="btn btn-light rounded-circle me-3 shadow-sm">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <div>
                    <h3 class="fw-bold mb-0">Tambah Chromebook</h3>
                    <p class="text-muted small">Daftarkan unit perangkat baru berdasarkan QR Code</p>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4 p-4">
                <form action="{{ route('chromebook.store') }}" method="POST">
                    @csrf
                    <div class="row g-4">
                        <div class="col-md-12">
                            <label class="form-label fw-bold">QR Code Unit (Unique ID)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 rounded-start-4">
                                    <i class="bi bi-qr-code-scan"></i>
                                </span>
                                <input type="text" name="qr_code_unit" id="qr_code_unit" 
                                    class="form-control form-control-lg border-start-0 rounded-end-4" 
                                    placeholder="Scan dengan alat atau ketik manual..." required autofocus>
                                
                                <button type="button" class="btn btn-primary ms-2 rounded-4 px-3" data-bs-toggle="modal" data-bs-target="#cameraModal">
                                    <i class="bi bi-camera-fill me-1"></i> Scan Kamera
                                </button>
                            </div>
                            <small class="text-muted d-block mt-2">
                                <i class="bi bi-info-circle me-1"></i> Klik kotak input jika menggunakan alat scanner fisik.
                            </small>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">Merek / Tipe</label>
                            <input type="text" name="merek" class="form-control rounded-3" placeholder="Contoh: Acer C733" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">Posisi Loker</label>
                            <input type="text" name="posisi_loker" class="form-control rounded-3" placeholder="Contoh: Loker A-01">
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-5">
                        <a href="{{ route('chromebook.index') }}" class="btn btn-light rounded-pill px-4">Batal</a>
                        <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold">
                            <i class="bi bi-save me-1"></i> Simpan Unit
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="cameraModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4">
            <div class="modal-header border-0 pb-0">
                <h5 class="fw-bold">Scan QR Code Unit</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" id="closeModal"></button>
            </div>
            <div class="modal-body text-center">
                <div id="reader" style="width: 100%; border-radius: 15px; overflow: hidden;"></div>
                <p class="text-muted small mt-3">Posisikan QR Code unit di depan kamera</p>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
    let html5QrcodeScanner;

    // Logika ketika modal kamera dibuka
    const cameraModal = document.getElementById('cameraModal');
    cameraModal.addEventListener('shown.bs.modal', function () {
        html5QrcodeScanner = new Html5QrcodeScanner("reader", { fps: 10, qrbox: 250 });
        html5QrcodeScanner.render(onScanSuccess);
    });

    // Logika ketika modal kamera ditutup
    cameraModal.addEventListener('hidden.bs.modal', function () {
        if (html5QrcodeScanner) {
            html5QrcodeScanner.clear();
        }
    });

    function onScanSuccess(decodedText) {
        document.getElementById('qr_code_unit').value = decodedText;
        document.getElementById('closeModal').click(); // Tutup modal otomatis
        
        // Alert sukses kecil
        Swal.fire({
            icon: 'success',
            title: 'Terdeteksi!',
            text: decodedText,
            timer: 1500,
            showConfirmButton: false
        });
    }
</script>
@endsection