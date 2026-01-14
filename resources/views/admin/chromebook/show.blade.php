@extends('layouts.app')

@section('title', 'Detail Unit ' . $unit->qr_code_unit)

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('chromebook.index') }}" class="btn btn-light rounded-circle me-3 shadow-sm">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div>
            <h3 class="fw-bold mb-0">Detail Perangkat</h3>
            <p class="text-muted small">Informasi teknis dan riwayat penggunaan unit</p>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="bg-primary p-5 text-center text-white">
                    <i class="bi bi-laptop display-1 opacity-50"></i>
                    <h4 class="fw-bold mt-3 mb-1">{{ $unit->qr_code_unit }}</h4>
                    <span class="badge {{ $unit->status == 'tersedia' ? 'bg-success' : 'bg-warning' }} rounded-pill px-3">
                        {{ ucfirst($unit->status) }}
                    </span>
                </div>
                <div class="card-body p-4">
                    <div class="mb-3">
                        <label class="text-muted small d-block">Merek / Tipe</label>
                        <span class="fw-bold">{{ $unit->merek }}</span>
                    </div>
                    <div class="mb-3">
                        <label class="text-muted small d-block">Posisi Loker</label>
                        <span class="fw-bold">{{ $unit->posisi_loker ?? '-' }}</span>
                    </div>
                    <div class="mb-0">
                        <label class="text-muted small d-block">Tanggal Terdaftar</label>
                        <span class="fw-bold">{{ $unit->created_at->format('d M Y') }}</span>
                    </div>
                    <hr>
                    <div class="d-grid gap-2">
                        <a href="{{ route('chromebook.edit', $unit->id) }}" class="btn btn-outline-primary rounded-pill btn-sm">
                            <i class="bi bi-pencil me-1"></i> Edit Informasi
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card border-0 shadow-sm rounded-4 p-4">
                <h5 class="fw-bold mb-4"><i class="bi bi-clock-history me-2 text-primary"></i>Riwayat Peminjaman</h5>
                
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="bg-light small text-muted">
                            <tr>
                                <th>SISWA</th>
                                <th>GURU / MAPEL</th>
                                <th>WAKTU PINJAM</th>
                                <th>KEMBALI</th>
                                <th>VOUCHER</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($histori as $h)
                            <tr>
                                <td>
                                    <div class="fw-bold small">{{ $h->siswa->nama_siswa }}</div>
                                    <div class="text-muted small">NIS: {{ $h->siswa->nis }}</div>
                                </td>
                                <td>
                                    <div class="small">{{ $h->guru->nama_guru }}</div>
                                    <div class="badge bg-light text-dark fw-normal border" style="font-size: 0.7rem;">
                                        {{ $h->mapel->nama_mapel }}
                                    </div>
                                </td>
                                <td class="small">{{ $h->waktu_pinjam->format('d/m/y H:i') }}</td>
                                <td>
                                    @if($h->waktu_kembali)
                                        <span class="text-success small">{{ $h->waktu_kembali->format('H:i') }}</span>
                                    @else
                                        <span class="badge bg-warning-subtle text-warning small border border-warning-subtle">Dipinjam</span>
                                    @endif
                                </td>
                                <td><code class="text-primary small">{{ $h->kode_voucher_given ?? '-' }}</code></td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted small">Belum ada riwayat penggunaan untuk unit ini.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    {{ $histori->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection