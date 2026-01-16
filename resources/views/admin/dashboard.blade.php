@extends('layouts.app')

@section('title', 'Dashboard Monitoring')

@section('content')
<div class="container-fluid py-3 py-md-4">
    {{-- Header Dashboard --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h3 class="fw-bold text-main mb-1">Ringkasan Sistem</h3>
            <p class="text-muted small mb-0">Pantau penggunaan unit dan stok voucher secara real-time.</p>
        </div>
    </div>

    {{-- Section Peringatan Voucher Kelas (Responsive Alert) --}}
    @if($stokKritisPerKelas->count() > 0)
    <div class="alert alert-danger border-0 shadow-sm rounded-4 p-3 p-md-4 mb-4 animate-pulse" role="alert">
        <div class="d-flex align-items-center mb-3">
            <div class="bg-danger text-white rounded-circle p-2 me-3 d-flex align-items-center justify-content-center" style="width: 45px; height: 45px; flex-shrink: 0;">
                <i class="bi bi-exclamation-octagon-fill fs-5"></i>
            </div>
            <div>
                <h6 class="alert-heading fw-bold mb-0">Stok Voucher Kelas Kritis!</h6>
                <p class="mb-0 small opacity-75">Beberapa kelas membutuhkan pengisian voucher segera.</p>
            </div>
        </div>
        <hr class="text-danger opacity-10">
        <div class="row g-2">
            @foreach($stokKritisPerKelas as $item)
            <div class="col-xl-2 col-lg-3 col-md-4 col-6">
                <div class="bg-white bg-opacity-50 border border-danger border-opacity-25 rounded-3 p-2 d-flex justify-content-between align-items-center">
                    <span class="small fw-bold text-dark text-truncate me-1"><i class="bi bi-door-open me-1"></i> {{ $item->nama_kelas }}</span>
                    <span class="badge bg-danger rounded-pill">{{ $item->vouchers_count }}</span>
                </div>
            </div>
            @endforeach
        </div>
        <div class="mt-3 text-end">
            <a href="{{ route('voucher.index') }}" class="btn btn-danger btn-sm rounded-pill px-4 fw-bold shadow-sm">
                <i class="bi bi-plus-circle me-1"></i> Kelola Voucher
            </a>
        </div>
    </div>
    @endif

    {{-- Section 4 Card Statistik (Grid 2 kolom di HP, 4 kolom di Desktop) --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3 col-6">
            <div class="card border-0 shadow-sm rounded-4 p-3 border-start border-primary border-5 h-100 card-hover">
                <div class="d-flex align-items-center">
                    <div class="bg-primary bg-opacity-10 p-2 p-md-3 rounded-4 me-2 me-md-3 text-primary d-none d-sm-flex">
                        <i class="bi bi-people-fill fs-3"></i>
                    </div>
                    <div>
                        <small class="text-muted d-block small fw-bold text-uppercase" style="font-size: 0.65rem;">Siswa Aktif</small>
                        <h4 class="fw-bold mb-0 text-main">{{ $stats['total_siswa'] }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card border-0 shadow-sm rounded-4 p-3 border-start border-success border-5 h-100 card-hover">
                <div class="d-flex align-items-center">
                    <div class="bg-success bg-opacity-10 p-2 p-md-3 rounded-4 me-2 me-md-3 text-success d-none d-sm-flex">
                        <i class="bi bi-check-circle-fill fs-3"></i>
                    </div>
                    <div>
                        <small class="text-muted d-block small fw-bold text-uppercase" style="font-size: 0.65rem;">Tersedia</small>
                        <h4 class="fw-bold mb-0 text-success">{{ $stats['unit_tersedia'] }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card border-0 shadow-sm rounded-4 p-3 border-start border-warning border-5 h-100 card-hover">
                <div class="d-flex align-items-center">
                    <div class="bg-warning bg-opacity-10 p-2 p-md-3 rounded-4 me-2 me-md-3 text-warning d-none d-sm-flex">
                        <i class="bi bi-laptop-fill fs-3"></i>
                    </div>
                    <div>
                        <small class="text-muted d-block small fw-bold text-uppercase" style="font-size: 0.65rem;">Dipinjam</small>
                        <h4 class="fw-bold mb-0 text-warning">{{ $stats['unit_dipinjam'] }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div id="voucherCard" class="card border-0 shadow-sm rounded-4 p-3 border-start border-5 h-100 
                {{ $stokKritisPerKelas->count() > 0 ? 'border-danger bg-danger bg-opacity-10 animate-pulse' : 'border-info bg-card' }} card-hover">
                <div class="d-flex align-items-center">
                    <div class="p-2 p-md-3 rounded-4 me-2 me-md-3 {{ $stokKritisPerKelas->count() > 0 ? 'text-danger' : 'text-info bg-info bg-opacity-10' }} d-none d-sm-flex">
                        <i class="bi bi-ticket-perforated-fill fs-3"></i>
                    </div>
                    <div>
                        <small class="text-muted d-block small fw-bold text-uppercase" style="font-size: 0.65rem;">Stok Voucher</small>
                        <h4 class="fw-bold mb-0 {{ $stokKritisPerKelas->count() > 0 ? 'text-danger' : 'text-main' }}">{{ $stats['stok_voucher'] }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Section Grafik (Satu Baris Penuh) --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-4 p-3 p-md-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="fw-bold mb-0 text-main">Tren Aktivitas Peminjaman</h5>
                    <span class="badge bg-light text-dark border rounded-pill px-3 py-2 d-none d-sm-block">7 Hari Terakhir</span>
                </div>
                <div style="position: relative; height: 250px; width: 100%;">
                    <canvas id="loanChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- Section Peminjam Saat Ini --}}
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-4 p-3 p-md-4">
                <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center mb-4 gap-3">
                    <h5 class="fw-bold mb-0 text-main">Data Peminjam Saat Ini</h5>
                    
                    {{-- Fitur Filter & Pencarian (Responsive Form) --}}
                    <form action="{{ route('admin.dashboard') }}" method="GET" class="d-flex flex-column flex-sm-row gap-2">
                        <div class="input-group input-group-sm shadow-xs">
                            <span class="input-group-text bg-card border-end-0 rounded-start-pill"><i class="bi bi-search text-muted"></i></span>
                            <input type="text" name="search" class="form-control bg-card border-start-0 rounded-end-pill" placeholder="Nama/Unit..." value="{{ request('search') }}">
                        </div>
                        <div class="d-flex gap-2">
                            <select name="kelas_id" class="form-select form-select-sm rounded-pill bg-card shadow-xs">
                                <option value="">Semua Kelas</option>
                                @foreach(App\Models\Kelas::orderBy('nama_kelas')->get() as $k)
                                    <option value="{{ $k->id }}" {{ request('kelas_id') == $k->id ? 'selected' : '' }}>{{ $k->nama_kelas }}</option>
                                @endforeach
                            </select>
                            <button type="submit" class="btn btn-dark btn-sm rounded-pill px-3">Filter</button>
                            @if(request('search') || request('kelas_id'))
                                <a href="{{ route('admin.dashboard') }}" class="btn btn-light btn-sm rounded-pill px-3"><i class="bi bi-arrow-clockwise"></i></a>
                            @endif
                        </div>
                    </form>
                </div>

                {{-- Tabel Responsive dengan Horizontal Scroll di Mobile --}}
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" style="min-width: 600px;">
                        <thead class="text-muted small">
                            <tr class="border-bottom">
                                <th class="py-3 px-2">UNIT</th>
                                <th class="py-3">SISWA</th>
                                <th class="py-3">KELAS</th>
                                <th class="py-3">WAKTU PINJAM</th>
                                <th class="py-3 text-end">STATUS</th>
                            </tr>
                        </thead>
                        <tbody class="border-0">
                            @forelse($peminjamAktif as $p)
                            <tr>
                                <td class="px-2">
                                    <span class="badge bg-primary rounded-pill px-3">Unit {{ $p->chromebook->no_unit }}</span>
                                </td>
                                <td>
                                    <div class="fw-bold text-main">{{ $p->siswa->nama_siswa }}</div>
                                    <small class="text-muted">{{ $p->siswa->nis }}</small>
                                </td>
                                <td><span class="badge bg-light text-dark">{{ $p->siswa->kelas->nama_kelas ?? '-' }}</span></td>
                                <td>
                                    <div class="small fw-medium"><i class="bi bi-clock me-1 text-primary"></i> {{ $p->waktu_pinjam->format('H:i') }}</div>
                                    <div class="text-muted" style="font-size: 0.75rem;">{{ $p->waktu_pinjam->diffForHumans() }}</div>
                                </td>
                                <td class="text-end">
                                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill px-3 py-2 small">
                                        Dipinjam
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted italic">
                                    <i class="bi bi-info-circle display-6 d-block mb-2 opacity-25"></i>
                                    Tidak ada data peminjaman aktif.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Global Overrides for Dark Mode Support */
    .text-main { color: var(--text-main) !important; }
    .bg-card { background-color: var(--card-bg) !important; }
    
    .card-hover:hover { transform: translateY(-5px); transition: 0.3s ease; }
    .shadow-xs { box-shadow: 0 2px 4px rgba(0,0,0,0.02); }
    .italic { font-style: italic; }

    @keyframes pulse-red {
        0% { transform: scale(1); box-shadow: 0 0 0 0 rgba(220, 53, 69, 0.4); }
        70% { transform: scale(1.02); box-shadow: 0 0 0 10px rgba(220, 53, 69, 0); }
        100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(220, 53, 69, 0); }
    }
    .animate-pulse { animation: pulse-red 2s infinite; }

    /* Responsive Adjustments */
    @media (max-width: 576px) {
        h3 { font-size: 1.25rem; }
        .card { border-radius: 1.25rem !important; }
        .btn-sm { padding: 0.4rem 0.8rem; }
    }
</style>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Notifikasi Stok Voucher
        const stokVoucher = {{ $stats['stok_voucher'] }};
        if (stokVoucher <= 50) {
            Swal.fire({
                title: 'Stok Voucher Kritis!',
                html: `Sisa voucher Anda saat ini adalah <b>${stokVoucher}</b>.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#0d6efd',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Kelola Sekarang',
                cancelButtonText: 'Abaikan',
                reverseButtons: true,
                customClass: { popup: 'rounded-4' },
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "{{ route('voucher.index') }}";
                }
            });
        }

        // Logic Chart (Auto-resize)
        const ctx = document.getElementById('loanChart').getContext('2d');
        const gradient = ctx.createLinearGradient(0, 0, 0, 400);
        gradient.addColorStop(0, 'rgba(67, 97, 238, 0.3)');
        gradient.addColorStop(1, 'rgba(67, 97, 238, 0)');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: {!! json_encode($days) !!},
                datasets: [{
                    label: 'Peminjaman',
                    data: {!! json_encode($counts) !!},
                    borderColor: '#4361ee',
                    backgroundColor: gradient,
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 4,
                    pointBackgroundColor: '#fff',
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { 
                        beginAtZero: true, 
                        grid: { borderDash: [5, 5], color: 'rgba(148, 163, 184, 0.1)' },
                        ticks: { stepSize: 1, color: '#94a3b8', font: { size: 10 } } 
                    },
                    x: { 
                        grid: { display: false },
                        ticks: { color: '#94a3b8', font: { size: 10 } }
                    }
                }
            }
        });
    });
</script>
@endsection