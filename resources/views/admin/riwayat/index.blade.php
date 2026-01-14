@extends('layouts.app')

@section('title', 'Laporan Eksklusif Riwayat Peminjaman')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4 no-print">
        <div>
            <h3 class="fw-bold text-dark mb-1">
                <i class="bi bi-file-earmark-bar-graph text-primary me-2"></i>Laporan Riwayat Peminjaman
            </h3>
            <p class="text-muted small mb-0">Pemantauan penggunaan unit Chromebook secara real-time dan akurat.</p>
        </div>
        <div class="d-flex gap-2">
            <button onclick="printAll()" class="btn btn-dark rounded-pill px-4 shadow-sm fw-bold transition-all">
                <i class="bi bi-printer-fill me-2"></i> Cetak Laporan Resmi
            </button>
        </div>
    </div>

    <div class="row g-3 mb-4 no-print">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden border-start border-primary border-5">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between">
                        <div>
                            <p class="text-muted small fw-bold mb-1">Total Aktivitas</p>
                            {{-- PERBAIKAN: Gunakan count() jika data adalah Collection (saat print) --}}
                            <h4 class="fw-bold mb-0 text-dark">
                                {{ request('print_all') ? $history->count() : $history->total() }}
                            </h4>
                        </div>
                        <div class="bg-primary bg-opacity-10 p-2 rounded-3">
                            <i class="bi bi-list-check text-primary fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden border-start border-warning border-5">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between">
                        <div>
                            <p class="text-muted small fw-bold mb-1">Sedang Dipinjam</p>
                            <h4 class="fw-bold mb-0 text-dark">{{ $history->where('waktu_kembali', null)->count() }}</h4>
                        </div>
                        <div class="bg-warning bg-opacity-10 p-2 rounded-3">
                            <i class="bi bi-clock-history text-warning fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden border-start border-success border-5">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between">
                        <div>
                            <p class="text-muted small fw-bold mb-1">Sudah Kembali</p>
                            <h4 class="fw-bold mb-0 text-dark">{{ $history->where('waktu_kembali', '!=', null)->count() }}</h4>
                        </div>
                        <div class="bg-success bg-opacity-10 p-2 rounded-3">
                            <i class="bi bi-check-all text-success fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{-- <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden border-start border-info border-5">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between">
                        <div>
                            <p class="text-muted small fw-bold mb-1">Persentase Sukses</p>
                            <h4 class="fw-bold mb-0 text-dark">
                                @php
                                    $totalCount = request('print_all') ? $history->count() : $history->total();
                                    $backCount = $history->where('waktu_kembali', '!=', null)->count();
                                @endphp
                                {{ $totalCount > 0 ? round(($backCount / $totalCount) * 100) : 0 }}%
                            </h4>
                        </div>
                        <div class="bg-info bg-opacity-10 p-2 rounded-3">
                            <i class="bi bi-percent text-info fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div> --}}
    </div>

    <div class="card border-0 shadow-sm rounded-4 mb-4 no-print">
        <div class="card-body p-4">
            <form action="{{ route('riwayat.index') }}" method="GET" id="filterForm">
                <div class="row g-3 align-items-end">
                    <div class="col-lg-3 col-md-6">
                        <label class="form-label small fw-bold text-secondary">Cari Siswa/Kelas/Unit</label>
                        <div class="input-group bg-light rounded-pill px-3">
                            <span class="input-group-text bg-transparent border-0"><i class="bi bi-search"></i></span>
                            <input type="text" name="search" class="form-control bg-transparent border-0 ps-0" 
                                   placeholder="Ketik kata kunci..." value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-3">
                        <label class="form-label small fw-bold text-secondary">Dari Tanggal</label>
                        <input type="date" name="start_date" id="start_date" class="form-control rounded-pill bg-light border-0 shadow-none" value="{{ request('start_date') }}">
                    </div>
                    <div class="col-lg-2 col-md-3">
                        <label class="form-label small fw-bold text-secondary">Sampai Tanggal</label>
                        <input type="date" name="end_date" id="end_date" class="form-control rounded-pill bg-light border-0 shadow-none" value="{{ request('end_date') }}">
                    </div>
                    <div class="col-lg-5 col-md-12 d-flex flex-wrap gap-2 justify-content-lg-end">
                        <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">
                            <i class="bi bi-funnel me-1"></i>Terapkan
                        </button>
                        
                        <div class="btn-group shadow-none border rounded-pill overflow-hidden bg-white">
                            <button type="button" onclick="setFilter('today')" class="btn btn-white btn-sm border-end px-3">Hari Ini</button>
                            <button type="button" onclick="setFilter('week')" class="btn btn-white btn-sm border-end px-3">Minggu</button>
                            <button type="button" onclick="setFilter('month')" class="btn btn-white btn-sm px-3">Bulan</button>
                        </div>
                        
                        <a href="{{ route('riwayat.index') }}" class="btn btn-light rounded-pill border shadow-sm px-3" title="Reset">
                            <i class="bi bi-arrow-clockwise"></i>
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="only-print">
        <table class="w-100 border-0 mb-3">
            <tr>
                <td style="width: 15%; text-align: center; border: none !important;">
                    @if(isset($settings['school_logo']))
                        <img src="{{ asset($settings['school_logo']) }}" style="width: 100px;">
                    @endif
                </td>
                <td style="width: 85%; text-align: center; border: none !important;" class="pe-5">
                    <h1 class="fw-bold text-uppercase mb-0" style="font-size: 22pt; color: #1a1a1a;">{{ $settings['school_name'] ?? 'DINAS PENDIDIKAN KOTA' }}</h1>
                    <p class="mb-0 fw-medium" style="font-size: 11pt;">{{ $settings['school_address'] ?? 'Alamat Lengkap Sekolah' }}</p>
                    <p class="mb-0 small fw-light">Telp: {{ $settings['school_phone'] ?? '-' }} | NPSN: {{ $settings['school_npsn'] ?? '-' }}</p>
                </td>
            </tr>
        </table>
        <div style="border-top: 4px double black; height: 5px; margin-bottom: 25px;"></div>
        
        <div class="text-center mb-4">
            <h4 class="fw-bold text-dark mb-1">LAPORAN PENGGUNAAN UNIT CHROMBOOK</h4>
            <p class="text-secondary small mb-0">
                @if(request('start_date'))
                    Periode: {{ \Carbon\Carbon::parse(request('start_date'))->translatedFormat('d M Y') }} - {{ \Carbon\Carbon::parse(request('end_date'))->translatedFormat('d M Y') }}
                @else
                    Filter: Rekapitulasi Menyeluruh
                @endif
            </p>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden border-top border-primary border-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="mainTable">
                <thead class="bg-primary text-white small">
                    <tr>
                        <th class="ps-4 py-3 text-center" style="width: 50px;">NO</th>
                        <th style="width: 120px;">NO UNIT</th>
                        <th>NAMA LENGKAP SISWA</th>
                        <th>MATA PELAJARAN / GURU</th>
                        <th>PINJAM</th>
                        <th>KEMBALI</th>
                        <th class="text-center no-print">STATUS</th>
                    </tr>
                </thead>
                <tbody class="text-dark">
                    @forelse($history as $index => $h)
                    <tr class="{{ $h->waktu_kembali ? '' : 'bg-warning bg-opacity-10' }}">
                        <td class="ps-4 text-center text-muted small">
                            {{ request('print_all') ? $loop->iteration : ($history->firstItem() + $index) }}
                        </td>
                        <td>
                            <span class="badge bg-secondary rounded-2 px-2 py-1 shadow-sm font-monospace">{{ $h->chromebook->no_unit }}</span>
                        </td>
                        <td>
                            <div class="fw-bold text-primary">{{ $h->siswa->nama_siswa }}</div>
                            <div class="text-muted" style="font-size: 0.75rem;"><i class="bi bi-door-open me-1"></i>Kelas: {{ $h->siswa->kelas->nama_kelas ?? '-' }}</div>
                        </td>
                        <td>
                            <div class="fw-bold small text-dark">{{ $h->mapel->nama_mapel ?? 'Umum' }}</div>
                            <div class="text-secondary italic" style="font-size: 0.75rem;"><i class="bi bi-person-workspace me-1"></i>{{ $h->guru->nama_guru ?? '-' }}</div>
                        </td>
                        <td class="small fw-medium">{{ $h->waktu_pinjam->format('d/m/Y') }}<br><span class="text-muted">{{ $h->waktu_pinjam->format('H:i') }}</span></td>
                        <td class="small fw-medium">
                            @if($h->waktu_kembali)
                                {{ $h->waktu_kembali->format('d/m/Y') }}<br><span class="text-muted">{{ $h->waktu_kembali->format('H:i') }}</span>
                            @else
                                <span class="text-danger fw-bold italic">Belum Kembali</span>
                            @endif
                        </td>
                        <td class="text-center no-print">
                            @if($h->waktu_kembali)
                                <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-2 border border-success border-opacity-25">
                                    <i class="bi bi-check-circle-fill me-1"></i>Selesai
                                </span>
                            @else
                                <span class="badge bg-warning bg-opacity-10 text-warning rounded-pill px-3 py-2 border border-warning border-opacity-25">
                                    <i class="bi bi-clock-fill me-1"></i>Dipinjam
                                </span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <i class="bi bi-folder2-open display-4 text-muted d-block mb-3"></i>
                            <span class="text-muted">Tidak ada data riwayat peminjaman yang dapat ditampilkan.</span>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- PERBAIKAN: Hanya jalankan hasPages() jika data adalah Paginator (bukan saat print) --}}
        @if(!request('print_all') && method_exists($history, 'hasPages') && $history->hasPages())
        <div class="card-footer bg-white border-0 py-4 px-4 no-print border-top">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div class="small text-muted fw-bold">Ditemukan {{ $history->total() }} total catatan aktivitas</div>
                <div class="pagination-exclusive">
                    {{ $history->appends(request()->input())->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<script>
    function setFilter(type) {
        const today = new Date().toISOString().split('T')[0];
        const startInput = document.getElementById('start_date');
        const endInput = document.getElementById('end_date');
        endInput.value = today;

        if (type === 'today') {
            startInput.value = today;
        } else if (type === 'week') {
            const lastWeek = new Date();
            lastWeek.setDate(lastWeek.getDate() - 7);
            startInput.value = lastWeek.toISOString().split('T')[0];
        } else if (type === 'month') {
            const lastMonth = new Date();
            lastMonth.setMonth(lastMonth.getMonth() - 1);
            startInput.value = lastMonth.toISOString().split('T')[0];
        }
        document.getElementById('filterForm').submit();
    }

    function printAll() {
        const url = new URL(window.location.href);
        url.searchParams.set('print_all', '1');
        const printWindow = window.open(url.toString(), '_blank');
        printWindow.onload = function() {
            setTimeout(() => { printWindow.print(); }, 800);
        };
    }
</script>

<style>
    :root { --primary-color: #0d6efd; }
    .only-print { display: none; }
    .btn-white { background: #fff; color: #4b5563; transition: all 0.2s; }
    .btn-white:hover { background: #f3f4f6; color: var(--primary-color); }
    .shadow-xs { box-shadow: 0 1px 2px rgba(0,0,0,0.05); }
    .italic { font-style: italic; }
    .transition-all { transition: all 0.3s ease; }
    .transition-all:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(0,0,0,0.1) !important; }

    @media print {
        @page { size: A4 portrait; margin: 1cm; }
        .no-print, .sidebar, .navbar, .card-footer, .btn, form, .main-content-header { display: none !important; }
        body, .main-content, .container-fluid { margin: 0 !important; padding: 0 !important; width: 100% !important; background: white !important; }
        .only-print { display: block !important; }
        .card { border: none !important; box-shadow: none !important; }
        table { width: 100% !important; border-collapse: collapse !important; border: 1.5px solid #000 !important; }
        th, td { border: 1px solid #000 !important; padding: 10px 8px !important; color: black !important; font-size: 8.5pt !important; }
        thead { background-color: #e5e7eb !important; }
        * { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
    }
</style>
@endsection