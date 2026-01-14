@extends('layouts.app')

@section('title', 'Manajemen Chromebook')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-0 text-dark">Manajemen Chromebook</h3>
            <p class="text-muted small">Kelola unit dan pantau status perangkat secara real-time</p>
        </div>
        <a href="{{ route('chromebook.create') }}" class="btn btn-primary rounded-pill px-4 shadow-sm fw-bold">
            <i class="bi bi-plus-lg me-1"></i> Unit Baru
        </a>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-muted small">
                    <tr>
                        <th class="ps-4 py-3">NO. UNIT / MEREK</th>
                        <th>QR CODE UNIT</th>
                        <th>LOKER</th>
                        <th>STATUS</th>
                        <th class="text-end pe-4">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($units as $unit)
                    <tr>
                        <td class="ps-4">
                            <div class="fw-bold text-dark">{{ $unit->no_unit }}</div>
                            <small class="text-muted">{{ $unit->merek }}</small>
                        </td>
                        <td>
                            <code class="text-primary small fw-bold bg-light px-2 py-1 rounded border shadow-xs">{{ $unit->qr_code_unit }}</code>
                        </td>
                        <td>
                            @if($unit->loker)
                                <span class="badge bg-white text-dark border fw-medium px-3 py-2 rounded-pill shadow-xs">
                                    <i class="bi bi-safe me-1 text-primary"></i> {{ $unit->loker }}
                                </span>
                            @else
                                <span class="text-muted small italic">- Belum diatur -</span>
                            @endif
                        </td>
                        <td>
                            @php
                                $color = [
                                    'tersedia' => 'success',
                                    'dipinjam' => 'warning',
                                    'rusak'    => 'danger'
                                ][$unit->status] ?? 'secondary';
                            @endphp
                            <span class="badge bg-{{ $color }}-subtle text-{{ $color }} px-3 py-2 rounded-pill border border-{{ $color }}-subtle fw-medium">
                                <i class="bi bi-circle-fill me-1" style="font-size: 0.4rem;"></i>
                                {{ ucfirst($unit->status) }}
                            </span>
                        </td>
                        <td class="text-end pe-4">
                            <div class="btn-group shadow-sm rounded-3 overflow-hidden border">
                                <a href="{{ route('chromebook.show', $unit->id) }}" class="btn btn-sm btn-white border-0" title="Lihat Detail">
                                    <i class="bi bi-eye text-info"></i>
                                </a>
                                <a href="{{ route('chromebook.edit', $unit->id) }}" class="btn btn-sm btn-white border-0 border-start" title="Edit Unit">
                                    <i class="bi bi-pencil-square text-warning"></i>
                                </a>
                                <form action="{{ route('chromebook.destroy', $unit->id) }}" method="POST" id="form-hapus-unit-{{ $unit->id }}" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn btn-sm btn-white border-0 border-start" title="Hapus Unit"
                                            onclick="confirmDelete('form-hapus-unit-{{ $unit->id }}', 'Unit {{ $unit->no_unit }}')">
                                        <i class="bi bi-trash3 text-danger"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5">
                            <div class="py-4">
                                <i class="bi bi-laptop display-1 text-muted opacity-25 d-block mb-3"></i>
                                <span class="text-muted">Data Chromebook tidak ditemukan.</span>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($units->hasPages())
        <div class="card-footer bg-white border-0 py-4 px-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center">
                <div class="text-muted small mb-3 mb-md-0">
                    Menampilkan <span class="fw-bold text-dark">{{ $units->firstItem() }}</span> sampai <span class="fw-bold text-dark">{{ $units->lastItem() }}</span> dari <span class="fw-bold text-dark">{{ $units->total() }}</span> unit Chromebook
                </div>
                <div class="pagination-container">
                    {{ $units->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<style>
    /* Styling Pagination Mewah */
    .pagination {
        margin-bottom: 0;
        gap: 6px;
    }
    .page-item .page-link {
        border-radius: 10px !important;
        border: 1px solid #eef2f7;
        color: #64748b;
        font-weight: 600;
        font-size: 0.85rem;
        padding: 8px 16px;
        transition: all 0.3s ease;
        box-shadow: 0 2px 4px rgba(0,0,0,0.02);
    }
    .page-item.active .page-link {
        background-color: var(--primary-color) !important;
        border-color: var(--primary-color) !important;
        color: #fff !important;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        transform: translateY(-2px);
    }
    .page-item:not(.active) .page-link:hover {
        background-color: #f8f9fa;
        color: var(--primary-color);
        border-color: var(--primary-color);
        transform: translateY(-2px);
    }
    .page-item.disabled .page-link {
        background-color: #fcfcfc;
        color: #cbd5e1;
    }

    /* Pendukung UI */
    .btn-white { background: #fff; }
    .btn-white:hover { background: #f8f9fa; }
    .btn-group .btn { padding: 0.5rem 0.9rem; }
    .shadow-xs { box-shadow: 0 1px 2px rgba(0,0,0,0.05); }
    .italic { font-style: italic; }
</style>
@endsection