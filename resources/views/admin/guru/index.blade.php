@extends('layouts.app')

@section('title', 'Manajemen Guru')

@section('content')
<div class="container-fluid py-3 py-md-4">
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1 text-main">Daftar Guru</h3>
            <p class="text-muted small mb-0">Kelola data guru pengajar</p>
        </div>
        <a href="{{ route('guru.create') }}" class="btn btn-primary rounded-pill px-4 shadow-sm">
            <i class="bi bi-person-plus me-1"></i> Tambah Guru
        </a>
    </div>

    {{-- Tabel Guru --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-muted small">
                    <tr>
                        <th class="ps-4 py-3" width="50">NO</th>
                        <th width="20%">NIP</th>
                        <th>NAMA GURU</th>
                        <th class="text-end pe-4" width="150">AKSI</th>
                    </tr>
                </thead>
                <tbody class="border-0">
                    @forelse($gurus as $guru)
                    <tr>
                        <td class="ps-4">
                            <span class="text-muted small">{{ ($gurus->currentPage() - 1) * $gurus->perPage() + $loop->iteration }}</span>
                        </td>
                        <td>
                            @if($guru->nip)
                                <span class="badge bg-light text-dark border fw-normal">{{ $guru->nip }}</span>
                            @else
                                <span class="text-muted small italic">Tidak ada NIP</span>
                            @endif
                        </td>
                        <td class="fw-bold text-dark text-uppercase" style="font-size: 0.85rem;">{{ $guru->nama_guru }}</td>
                        <td class="text-end pe-4">
                            <div class="btn-group shadow-sm rounded-3 overflow-hidden border">
                                <a href="{{ route('guru.edit', $guru->id) }}" class="btn btn-sm btn-white border-0" title="Edit">
                                    <i class="bi bi-pencil-square text-warning"></i>
                                </a>
                                <form action="{{ route('guru.destroy', $guru->id) }}" method="POST" onsubmit="return confirm('Hapus data guru ini?')" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-white border-0 border-start" title="Hapus">
                                        <i class="bi bi-trash3 text-danger"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center py-5">
                            <i class="bi bi-people display-4 text-muted opacity-25 d-block mb-3"></i>
                            <span class="text-muted italic">Belum ada data guru.</span>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        {{-- Bagian Pagination: Diselaraskan dengan Kelas/Siswa --}}
        @if($gurus->hasPages())
        <div class="card-footer bg-card border-0 py-4 px-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                <div class="text-muted small order-2 order-md-1">
                    Menampilkan <b>{{ $gurus->firstItem() }}</b> - <b>{{ $gurus->lastItem() }}</b> dari <b>{{ $gurus->total() }}</b> guru
                </div>
                <div class="pagination-container order-1 order-md-2">
                    {{ $gurus->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<style>
    /* Mengikuti gaya yang sama dengan siswa/index dan kelas/index */
    .text-main { color: var(--text-main) !important; }
    .bg-card { background-color: var(--card-bg) !important; }

    .pagination { margin-bottom: 0; gap: 4px; flex-wrap: wrap; justify-content: center; }
    .page-item .page-link { border-radius: 8px !important; border: 1px solid #eef2f7; color: #64748b; font-weight: 600; font-size: 0.8rem; padding: 6px 12px; }
    .page-item.active .page-link { background-color: var(--primary-color) !important; border-color: var(--primary-color) !important; color: #fff !important; }
    
    .btn-white { background: var(--card-bg); color: var(--text-main); }
    .shadow-xs { box-shadow: 0 1px 2px rgba(0,0,0,0.05); }
    .italic { font-style: italic; }

    /* Custom scrollbar untuk table-responsive */
    .table-responsive::-webkit-scrollbar { height: 6px; }
    .table-responsive::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }

    @media (max-width: 576px) {
        .card { border-radius: 1rem !important; }
    }
</style>
@endsection