@extends('layouts.app')

@section('title', 'Detail Unit ' . $unit->qr_code_unit)

@section('content')
<div class="container-fluid py-3 py-md-4">
    {{-- Header Section --}}
    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('chromebook.index') }}" class="btn btn-light rounded-circle me-3 shadow-sm border">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div>
            <h3 class="fw-bold mb-0 text-main">Detail Perangkat</h3>
            <p class="text-muted small mb-0">Informasi teknis dan riwayat penggunaan unit</p>
        </div>
    </div>

    <div class="row g-3 g-md-4">
        {{-- Card Informasi Unit (Kiri) --}}
        <div class="col-12 col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100">
                <div class="bg-primary p-4 p-md-5 text-center text-white position-relative">
                    {{-- Ikon melayang agar lebih modern --}}
                    <i class="bi bi-laptop display-1 opacity-25 position-absolute top-50 start-50 translate-middle"></i>
                    <div class="position-relative" style="z-index: 1;">
                        <h4 class="fw-bold mt-2 mb-2">{{ $unit->qr_code_unit }}</h4>
                        @php
                            $statusClass = $unit->status == 'tersedia' ? 'bg-success' : 'bg-warning text-dark';
                        @endphp
                        <span class="badge {{ $statusClass }} rounded-pill px-3 py-2 border border-white border-opacity-25 shadow-sm">
                            <i class="bi bi-circle-fill me-1 small"></i> {{ ucfirst($unit->status) }}
                        </span>
                    </div>
                </div>
                <div class="card-body p-4 bg-card">
                    <div class="row g-3">
                        <div class="col-6 col-lg-12">
                            <label class="text-muted small d-block mb-1">Merek / Tipe</label>
                            <span class="fw-bold text-main d-block text-truncate">{{ $unit->merek }}</span>
                        </div>
                        <div class="col-6 col-lg-12">
                            <label class="text-muted small d-block mb-1">Posisi Loker</label>
                            <span class="fw-bold text-main d-block text-truncate">{{ $unit->posisi_loker ?? '-' }}</span>
                        </div>
                        <div class="col-12">
                            <label class="text-muted small d-block mb-1">Tanggal Terdaftar</label>
                            <span class="fw-bold text-main d-block">{{ $unit->created_at->translatedFormat('d F Y') }}</span>
                        </div>
                    </div>
                    
                    <hr class="my-4 opacity-50">
                    
                    <div class="d-grid">
                        <a href="{{ route('chromebook.edit', $unit->id) }}" class="btn btn-outline-primary rounded-pill fw-bold">
                            <i class="bi bi-pencil-square me-2"></i>Edit Informasi
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card Riwayat (Kanan) --}}
        <div class="col-12 col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 p-3 p-md-4 h-100 bg-card">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="fw-bold mb-0 text-main">
                        <i class="bi bi-clock-history me-2 text-primary"></i>Riwayat Penggunaan
                    </h5>
                    <span class="badge bg-light text-muted border rounded-pill px-3">{{ $histori->total() }} Sesi</span>
                </div>
                
                {{-- Tabel Responsive dengan scroll horizontal di mobile --}}
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" style="min-width: 650px;">
                        <thead class="text-muted small uppercase">
                            <tr class="border-bottom">
                                <th class="py-3">SISWA</th>
                                <th class="py-3">GURU / MAPEL</th>
                                <th class="py-3">WAKTU PINJAM</th>
                                <th class="py-3">KEMBALI</th>
                                <th class="py-3">VOUCHER</th>
                            </tr>
                        </thead>
                        <tbody class="border-0">
                            @forelse($histori as $h)
                            <tr>
                                <td>
                                    <div class="fw-bold text-main mb-0">{{ $h->siswa->nama_siswa }}</div>
                                    <small class="text-muted">NIS: {{ $h->siswa->nis }}</small>
                                </td>
                                <td>
                                    <div class="small fw-medium text-main">{{ $h->guru->nama_guru }}</div>
                                    <span class="badge bg-primary-subtle text-primary fw-normal border border-primary-subtle" style="font-size: 0.65rem;">
                                        {{ $h->mapel->nama_mapel }}
                                    </span>
                                </td>
                                <td>
                                    <div class="small text-main fw-medium">{{ $h->waktu_pinjam->format('d/m/y') }}</div>
                                    <div class="text-muted" style="font-size: 0.75rem;"><i class="bi bi-clock me-1"></i> {{ $h->waktu_pinjam->format('H:i') }}</div>
                                </td>
                                <td>
                                    @if($h->waktu_kembali)
                                        <div class="small text-success fw-bold"><i class="bi bi-check2-circle me-1"></i> {{ $h->waktu_kembali->format('H:i') }}</div>
                                    @else
                                        <span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill px-2 py-1" style="font-size: 0.7rem;">
                                            Masih Dipinjam
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @if($h->kode_voucher_given)
                                        <code class="text-primary fw-bold" style="font-size: 0.85rem;">{{ $h->kode_voucher_given }}</code>
                                    @else
                                        <span class="text-muted small">-</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-5">
                                    <i class="bi bi-folder2-open display-4 text-muted opacity-25 d-block mb-3"></i>
                                    <span class="text-muted italic">Belum ada riwayat penggunaan unit ini.</span>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination yang responsif --}}
                <div class="mt-4 d-flex justify-content-center">
                    {{ $histori->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Sinkronisasi warna dengan Dark Mode Ibu */
    .text-main { color: var(--text-main) !important; }
    .bg-card { background-color: var(--card-bg) !important; }
    
    .table-responsive::-webkit-scrollbar { height: 6px; }
    .table-responsive::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
    
    .italic { font-style: italic; }

    @media (max-width: 992px) {
        .display-1 { font-size: 4rem; }
    }
</style>
@endsection