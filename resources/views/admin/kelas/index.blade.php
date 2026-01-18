@extends('layouts.app')

@section('title', 'Manajemen Kelas')

@section('content')
<div class="container-fluid py-3 py-md-4">
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1 text-main">Manajemen Kelas</h3>
            <p class="text-muted small mb-0">Kelola data kelas dan penetapan wali kelas</p>
        </div>
        <a href="{{ route('kelas.create') }}" class="btn btn-primary rounded-pill px-4 shadow-sm">
            <i class="bi bi-plus-circle me-1"></i> Tambah Kelas
        </a>
    </div>

    {{-- Tabel Kelas --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-muted small">
                    <tr>
                        <th class="ps-4 py-3" width="50">NO</th>
                        <th width="25%">NAMA KELAS</th>
                        <th>WALI KELAS</th>
                        <th class="text-center">JUMLAH SISWA</th>
                        <th class="text-end pe-4" width="150">AKSI</th>
                    </tr>
                </thead>
                <tbody class="border-0">
                    @forelse($kelas as $k)
                    <tr>
                        <td class="ps-4">
                            <span class="text-muted small">{{ ($kelas->currentPage() - 1) * $kelas->perPage() + $loop->iteration }}</span>
                        </td>
                        <td class="fw-bold text-dark">{{ $k->nama_kelas }}</td>
                        <td>
                            @if($k->wali)
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-person-badge text-primary me-2"></i>
                                    <span class="text-main" style="font-size: 0.9rem;">{{ $k->wali->nama_guru }}</span>
                                </div>
                            @else
                                <span class="text-muted small italic">Belum ada wali</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-2 shadow-xs">
                                {{ $k->siswas_count ?? 0 }} Siswa
                            </span>
                        </td>
                        <td class="text-end pe-4">
                            <div class="btn-group shadow-sm rounded-3 overflow-hidden border">
                                <a href="{{ route('kelas.edit', $k->id) }}" class="btn btn-sm btn-white border-0" title="Edit Data">
                                    <i class="bi bi-pencil-square text-warning"></i>
                                </a>
                                <form action="{{ route('kelas.destroy', $k->id) }}" method="POST" id="delete-form-{{ $k->id }}" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button type="button" class="btn btn-sm btn-white border-0 border-start" 
                                            onclick="confirmDelete('delete-form-{{ $k->id }}', '{{ $k->nama_kelas }}')" title="Hapus">
                                        <i class="bi bi-trash text-danger"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5">
                            <i class="bi bi-door-closed display-4 text-muted opacity-25 d-block mb-3"></i>
                            <span class="text-muted italic">Belum ada data kelas.</span>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination: Mengikuti gaya halaman siswa --}}
        @if($kelas->hasPages())
        <div class="card-footer bg-card border-0 py-4 px-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                <div class="text-muted small order-2 order-md-1">
                    Menampilkan <b>{{ $kelas->firstItem() }}</b> - <b>{{ $kelas->lastItem() }}</b> dari <b>{{ $kelas->total() }}</b> kelas
                </div>
                <div class="pagination-container order-1 order-md-2">
                    {{ $kelas->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

{{-- Script Konfirmasi Hapus --}}
<script>
    function confirmDelete(formId, itemName) {
        if (confirm('Apakah Anda yakin ingin menghapus kelas ' + itemName + '?')) {
            document.getElementById(formId).submit();
        }
    }
</script>

<style>
    /* Mengikuti gaya yang sama dengan siswa/index */
    .text-main { color: var(--text-main) !important; }
    .bg-card { background-color: var(--card-bg) !important; }

    .pagination { margin-bottom: 0; gap: 4px; flex-wrap: wrap; justify-content: center; }
    .page-item .page-link { border-radius: 8px !important; border: 1px solid #eef2f7; color: #64748b; font-weight: 600; font-size: 0.8rem; padding: 6px 12px; }
    .page-item.active .page-link { background-color: var(--primary-color) !important; border-color: var(--primary-color) !important; color: #fff !important; }
    
    .btn-white { background: var(--card-bg); color: var(--text-main); }
    .shadow-xs { box-shadow: 0 1px 2px rgba(0,0,0,0.05); }
    .italic { font-style: italic; }

    /* Custom scrollbar */
    .table-responsive::-webkit-scrollbar { height: 6px; }
    .table-responsive::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }

    @media (max-width: 576px) {
        .card { border-radius: 1rem !important; }
    }
</style>
@endsection