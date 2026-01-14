@extends('layouts.app')

@section('title', 'Dashboard Monitoring')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <h3 class="fw-bold text-dark">Ringkasan Sistem</h3>
            <p class="text-muted">Pantau penggunaan unit dan stok voucher secara real-time.</p>
            
            @if($stats['stok_voucher'] <= 20)
            <div class="alert alert-danger border-0 shadow-sm rounded-4 d-flex align-items-center p-3 mb-4" role="alert">
                <div class="bg-danger text-white rounded-circle p-2 me-3 d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                    <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                </div>
                <div>
                    <h6 class="alert-heading fw-bold mb-1">Peringatan: Stok Voucher Menipis!</h6>
                    <p class="mb-0 small">Sisa voucher saat ini hanya <strong>{{ $stats['stok_voucher'] }}</strong>.</p>
                </div>
                <div class="ms-auto">
                    <a href="{{ route('voucher.index') }}" class="btn btn-danger btn-sm rounded-pill px-3 fw-bold">Tambah Voucher</a>
                </div>
            </div>
            @endif
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 border-start border-secondary border-5 h-100">
                <div class="d-flex align-items-center">
                    <div class="bg-success bg-opacity-10 p-3 rounded-4 me-3 d-flex align-items-center justify-content-center">
                        <i class="fi fi-rs-users-class fs-3 text-secondary"></i>
                    </div>
                    <div>
                        <small class="text-muted d-block small fw-bold">SISWA AKTIF</small>
                        <h4 class="fw-bold mb-0">{{ $stats['total_siswa'] }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 border-start border-success border-5 h-100">
                <div class="d-flex align-items-center">
                    <div class="bg-success bg-opacity-10 p-3 rounded-4 me-3 text-success">
                        <i class="bi bi-check-circle-fill fs-3"></i>
                    </div>
                    <div>
                        <small class="text-muted d-block small fw-bold">UNIT TERSEDIA</small>
                        <h4 class="fw-bold mb-0">{{ $stats['unit_tersedia'] }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 border-start border-warning border-5 h-100">
                <div class="d-flex align-items-center">
                    <div class="bg-warning bg-opacity-10 p-3 rounded-4 me-3 text-warning">
                        <i class="bi bi-laptop-fill fs-3"></i>
                    </div>
                    <div>
                        <small class="text-muted d-block small fw-bold">SEDANG DIPINJAM</small>
                        <h4 class="fw-bold mb-0">{{ $stats['unit_dipinjam'] }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div id="voucherCard" class="card border-0 shadow-sm rounded-4 p-3 border-start border-5 h-100 
                {{ $stats['stok_voucher'] <= 20 ? 'border-danger bg-danger bg-opacity-10 animate-pulse' : 'border-info bg-white' }}">
                <div class="d-flex align-items-center">
                    <div class="p-3 rounded-4 me-3 {{ $stats['stok_voucher'] <= 20 ? 'text-danger' : 'text-info bg-info bg-opacity-10' }}">
                        <i class="bi bi-ticket-perforated-fill fs-3"></i>
                    </div>
                    <div>
                        <small class="text-muted d-block small fw-bold">STOK VOUCHER</small>
                        <h4 class="fw-bold mb-0 {{ $stats['stok_voucher'] <= 20 ? 'text-danger' : '' }}">
                            {{ $stats['stok_voucher'] }}
                        </h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm rounded-4 p-4">
                <h5 class="fw-bold mb-4">Tren Peminjaman</h5>
                <div style="position: relative; height: 320px; width: 100%;">
                    <canvas id="loanChart"></canvas>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
                <h5 class="fw-bold mb-3">Peminjam Saat Ini</h5>
                <div class="list-group list-group-flush">
                    @forelse($peminjamAktif as $p)
                    <div class="list-group-item px-0 border-0 mb-3 d-flex align-items-center">
                        <div class="bg-primary text-white p-2 rounded-3 me-3 text-center" style="min-width: 55px;">
                            <small class="d-block text-uppercase" style="font-size: 0.55rem;">Unit</small>
                            <span class="fw-bold">{{ $p->chromebook->no_unit }}</span>
                        </div>
                        <div>
                            <h6 class="mb-0 fw-bold small text-dark">{{ $p->siswa->nama_siswa }}</h6>
                            <small class="text-muted small"><i class="bi bi-clock me-1"></i> {{ $p->waktu_pinjam->diffForHumans() }}</small>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-5">
                        <i class="bi bi-info-circle text-muted display-6 d-block mb-2"></i>
                        <p class="text-muted small">Semua unit tersedia.</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    @keyframes pulse-red {
        0% { transform: scale(1); }
        50% { transform: scale(1.02); }
        100% { transform: scale(1); }
    }
    .animate-pulse { animation: pulse-red 2s infinite ease-in-out; }
    .card:hover { transform: translateY(-5px); transition: 0.3s; }
</style>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const stokVoucher = {{ $stats['stok_voucher'] }};

        // 1. POP-UP SWEETALERT JIKA STOK KRITIS
        if (stokVoucher <= 20) {
            Swal.fire({
                title: 'Stok Voucher Kritis!',
                text: `Sisa voucher Anda saat ini adalah ${stokVoucher}. Segera lakukan pengisian ulang agar operasional tidak terganggu.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Tambah Voucher Sekarang',
                cancelButtonText: 'Nanti Saja',
                backdrop: `rgba(255,0,0,0.1)`
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "{{ route('voucher.index') }}";
                }
            });
        }

        // 2. LOGIKA GRAFIK
        const ctx = document.getElementById('loanChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: {!! json_encode($days) !!},
                datasets: [{
                    label: 'Peminjaman',
                    data: {!! json_encode($counts) !!},
                    borderColor: '#0d6efd',
                    backgroundColor: 'rgba(13, 110, 253, 0.05)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, ticks: { stepSize: 1 } },
                    x: { grid: { display: false } }
                }
            }
        });
    });
</script>
@endsection