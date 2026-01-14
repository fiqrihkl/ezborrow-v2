@extends('layouts.app')

@section('title', 'Mode Scan Manual - EZBorrow')

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
    }

    .unified-card {
        width: 100%; 
        max-width: 400px; 
        height: auto;
        min-height: 450px;
        background: rgba(255, 255, 255, 0.85);
        backdrop-filter: blur(15px);
        -webkit-backdrop-filter: blur(15px);
        border-radius: 30px;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        position: relative; 
        border: 1px solid rgba(255, 255, 255, 0.7);
        padding: 30px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
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

    .icon-box {
        width: 80px;
        height: 80px;
        background: rgba(67, 97, 238, 0.1);
        border-radius: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 20px;
        color: var(--primary-color);
        font-size: 2rem;
    }

    .custom-input {
        background: #f8fafc !important;
        border: 2px solid #e2e8f0 !important;
        border-radius: 15px !important;
        padding: 15px !important;
        font-weight: 700 !important;
        text-align: center !important;
        letter-spacing: 2px;
        font-size: 1.2rem !important;
        transition: all 0.3s ease;
    }

    .custom-input:focus {
        border-color: var(--primary-color) !important;
        box-shadow: 0 0 0 4px rgba(67, 97, 238, 0.1) !important;
    }

    .btn-action {
        height: 55px;
        border-radius: 15px;
        font-weight: 700;
        letter-spacing: 0.5px;
        background: var(--primary-color);
        border: none;
        transition: all 0.3s ease;
    }

    .btn-action:hover {
        background: #3a0ca3;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(67, 97, 238, 0.3);
    }

    .system-footer {
        margin-top: 20px;
        padding-top: 15px;
        border-top: 1px dashed #e2e8f0;
    }

    .footer-text {
        font-size: 8px;
        letter-spacing: 1px;
        color: #94a3b8;
        text-transform: uppercase;
    }
</style>
@endsection

@section('content')
<div class="scan-page-wrapper">
    <div class="unified-card">
        <div class="text-center">
            <div class="icon-box">
                <i class="fas fa-keyboard"></i>
            </div>
            <span class="badge bg-primary px-3 py-2 rounded-pill mb-2">MODE MANUAL</span>
            <h4 class="fw-bold text-dark mb-1">Input Kode ID</h4>
            <p class="text-muted small">Ketik NIS atau Scan dengan Barcode Scanner</p>
        </div>

        <form action="{{ route('peminjaman.final') }}" method="POST" class="my-4">
    @csrf
    <div class="mb-4">
        <input type="text" name="qr_chromebook" class="form-control custom-input" 
               placeholder="NOMOR NIS / ID" autocomplete="off" required autofocus>
    </div>
    <button type="submit" class="btn btn-primary btn-action w-100 shadow-sm">
        LANJUTKAN PROSES <i class="fas fa-arrow-right ms-2"></i>
    </button>
</form>

        <div class="text-center">
            <a href="{{ route('pinjam.index') }}" class="btn btn-link text-decoration-none text-muted fw-bold small">
                <i class="fas fa-home me-1"></i> KEMBALI KE MENU UTAMA
            </a>
            
            <div class="system-footer">
                <div class="footer-text">
                    <strong>EZBORROW v2.0</strong> • Digital Library<br>
                    <span class="text-primary fw-bolder">Developed by Fiqri Haikal</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Perbaikan Tampilan Voucher agar sama dengan Scan Kamera
    @if(session('voucher_baru'))
        Swal.fire({
            title: 'PEMINJAMAN BERHASIL',
            html: `
                <div style="padding: 10px; font-family: 'Inter', sans-serif;">
                    <p style="color: #64748b; font-size: 13px; margin-bottom: 20px;">Selamat! Peminjaman Anda telah tercatat.<br>Berikut akses WiFi khusus Anda:</p>
                    
                    <div style="background: linear-gradient(135deg, #4361ee 0%, #3a0ca3 100%); 
                                border-radius: 20px; 
                                padding: 25px; 
                                color: white; 
                                box-shadow: 0 15px 30px rgba(67, 97, 238, 0.3);
                                position: relative;
                                overflow: hidden;
                                margin-bottom: 20px;
                                border: 1px solid rgba(255,255,255,0.1);">
                        
                        <div style="position: absolute; top: -20px; right: -20px; width: 100px; height: 100px; background: rgba(255,255,255,0.1); border-radius: 50%;"></div>
                        <div style="position: absolute; bottom: -30px; left: -10px; width: 80px; height: 80px; background: rgba(255,255,255,0.05); border-radius: 50%;"></div>

                        <div style="position: relative; z-index: 1;">
                            <div style="display: flex; justify-content: center; align-items: center; gap: 8px; margin-bottom: 15px;">
                                <i class="fas fa-wifi" style="font-size: 12px; opacity: 0.8;"></i>
                                <span style="text-transform: uppercase; letter-spacing: 2px; font-size: 10px; font-weight: 600;">Internet Access Voucher</span>
                            </div>
                            
                            <div style="background: rgba(255,255,255,0.15); 
                                        backdrop-filter: blur(5px); 
                                        border-radius: 12px; 
                                        padding: 15px; 
                                        margin: 10px 0;
                                        border: 1px dashed rgba(255,255,255,0.3);">
                                <h2 style="font-family: 'Monaco', 'Courier New', monospace; 
                                           font-weight: 800; 
                                           letter-spacing: 6px; 
                                           margin: 0; 
                                           font-size: 2.2rem;
                                           text-shadow: 0 2px 4px rgba(0,0,0,0.2);">
                                    {{ session('voucher_baru') }}
                                </h2>
                            </div>
                            
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 20px; font-size: 9px; opacity: 0.8; font-weight: 500;">
                                <span>SISTEM EZBORROW v2.0</span>
                                <span>VALID UNTIL: {{ date('H:i', strtotime('+2 hours')) }} WITA</span>
                            </div>
                        </div>
                    </div>

                    <p style="color: #ef4444; font-size: 11px; font-weight: 700; margin-top: 15px;">
                        ⚠️ MOHON CATAT KODE SEBELUM MENUTUP JENDELA INI!
                    </p>
                </div>
            `,
            confirmButtonText: '<i class="fas fa-check-circle me-2"></i> SAYA SUDAH CATAT',
            confirmButtonColor: '#4361ee',
            allowOutsideClick: false,
            width: '400px',
            customClass: {
                popup: 'rounded-5 shadow-lg border-0',
                confirmButton: 'rounded-pill px-4 py-2 fw-bold'
            }
        });
    @endif

    @if(session('error'))
        Swal.fire({
            title: 'Oops!',
            text: "{{ session('error') }}",
            icon: 'error',
            confirmButtonColor: '#4361ee',
            customClass: { popup: 'rounded-5' }
        });
    @endif
</script>
@endsection