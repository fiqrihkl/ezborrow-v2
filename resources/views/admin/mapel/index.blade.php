@extends('layouts.app')

@section('title', 'Manajemen Mata Pelajaran')

@section('content')
<div class="container-fluid py-3 py-md-4">
    {{-- Header --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h3 class="fw-bold mb-1 text-main">Mata Pelajaran</h3>
            <p class="text-muted small mb-0">Kelola kurikulum dan penugasan guru pengampu</p>
        </div>
        <a href="{{ route('mapel.create') }}" class="btn btn-primary rounded-pill px-4 shadow-sm flex-fill flex-md-grow-0">
            <i class="bi bi-plus-lg me-1"></i> Tambah Mapel
        </a>
    </div>

    {{-- Tabel Data --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="min-width: 800px;">
                <thead class="bg-light text-muted small">
                    <tr>
                        <th class="ps-4 py-3" width="120">KODE</th>
                        <th>NAMA MATA PELAJARAN</th>
                        <th>DESKRIPSI</th>
                        <th>GURU PENGAMPU</th>
                        <th class="text-end pe-4" width="150">AKSI</th>
                    </tr>
                </thead>
                <tbody class="border-0">
                    @forelse($mapels as $m)
                    <tr>
                        <td class="ps-4">
                            <span class="badge bg-white text-primary border fw-bold px-3 py-2 rounded-3 shadow-xs">
                                {{ $m->kode_mapel }}
                            </span>
                        </td>
                        <td>
                            <div class="fw-bold text-main">{{ $m->nama_mapel }}</div>
                        </td>
                        <td>
                            <small class="text-muted italic">{{ Str::limit($m->deskripsi, 50) ?? '-' }}</small>
                        </td>
                        <td>
                            <div class="d-flex flex-wrap gap-1">
                                @forelse($m->gurus as $g)
                                    <span class="badge bg-info-subtle text-info border border-info-subtle rounded-pill px-2 py-1 fw-normal" style="font-size: 0.7rem;">
                                        <i class="bi bi-person-fill me-1"></i>{{ $g->nama_guru }}
                                    </span>
                                @empty
                                    <span class="text-muted small italic" style="font-size: 0.75rem;">Belum ada guru</span>
                                @endforelse
                            </div>
                        </td>
                        <td class="text-end pe-4">
                            <div class="btn-group shadow-sm rounded-3 overflow-hidden border">
                                <a href="{{ route('mapel.edit', $m->id) }}" class="btn btn-sm btn-white border-0" title="Edit Mapel">
                                    <i class="bi bi-pencil text-warning"></i>
                                </a>
                                {{-- Tombol Hapus dengan SweetAlert --}}
                                <button type="button" class="btn btn-sm btn-white border-0 border-start" 
                                    onclick="confirmDeleteMapel('{{ $m->id }}', '{{ $m->nama_mapel }}')">
                                    <i class="bi bi-trash text-danger"></i>
                                </button>
                            </div>
                            {{-- Form Hidden untuk Delete --}}
                            <form id="delete-form-{{ $m->id }}" action="{{ route('mapel.destroy', $m->id) }}" method="POST" style="display:none;">
                                @csrf @method('DELETE')
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5">
                            <div class="py-4">
                                <i class="bi bi-book-half display-1 opacity-10 d-block mb-3 text-muted"></i>
                                <span class="text-muted italic">Data mata pelajaran tidak ditemukan.</span>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($mapels->hasPages())
        <div class="card-footer bg-card border-0 py-4 px-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                <div class="text-muted small order-2 order-md-1">
                    Menampilkan <b>{{ $mapels->firstItem() }}</b> - <b>{{ $mapels->lastItem() }}</b> dari <b>{{ $mapels->total() }}</b> mapel
                </div>
                <div class="pagination-container order-1 order-md-2">
                    {{ $mapels->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<style>
    :root {
        --primary-color: #4361ee;
        --text-main: #2d3748;
        --card-bg: #ffffff;
    }
    .text-main { color: var(--text-main) !important; }
    .bg-card { background-color: var(--card-bg) !important; }
    .shadow-xs { box-shadow: 0 1px 2px rgba(0,0,0,0.05); }
    .italic { font-style: italic; }
    .btn-white { background: #fff; color: var(--text-main); }
    .btn-white:hover { background: #f8fafc; }
    .pagination { margin-bottom: 0; gap: 4px; }
    .page-item .page-link { border-radius: 8px !important; border: 1px solid #eef2f7; color: #64748b; font-weight: 600; font-size: 0.8rem; }
    .page-item.active .page-link { background-color: var(--primary-color) !important; border-color: var(--primary-color) !important; }
</style>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function confirmDeleteMapel(id, name) {
        Swal.fire({
            title: 'Hapus Mata Pelajaran?',
            html: `
                <div class="text-center">
                    <p>Anda akan menghapus mapel <b>${name}</b>.</p>
                    <div class="alert alert-danger small rounded-3 border-0">
                        <i class="bi bi-exclamation-triangle-fill me-1"></i> 
                        <strong>Peringatan Penting:</strong> Menghapus data ini akan memutus hubungan dengan guru pengampu, menghapus data nilai, dan jadwal yang berkaitan dengan mata pelajaran ini secara permanen.
                    </div>
                    <p class="mb-0 small text-muted">Apakah Anda yakin ingin melanjutkan?</p>
                </div>
            `,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Ya, Hapus Permanen!',
            cancelButtonText: 'Batal',
            reverseButtons: true,
            customClass: {
                popup: 'rounded-4 border-0 shadow-lg',
                confirmButton: 'rounded-pill px-4',
                cancelButton: 'rounded-pill px-4'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                // Tampilkan loading saat proses hapus
                Swal.fire({
                    title: 'Sedang menghapus...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading()
                    }
                });
                document.getElementById('delete-form-' + id).submit();
            }
        });
    }

    // Menampilkan notifikasi sukses jika ada session success
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: "{{ session('success') }}",
            timer: 3000,
            showConfirmButton: false,
            toast: true,
            position: 'top-end'
        });
    @endif
</script>
@endsection