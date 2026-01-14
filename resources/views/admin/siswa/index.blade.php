@extends('layouts.app')

@section('title', 'Manajemen Siswa')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-0 text-dark">Data Siswa</h3>
            <p class="text-muted small">Kelola data siswa aktif dan pantau status peminjaman</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('siswa.export') }}" class="btn btn-outline-success rounded-pill px-3 shadow-sm">
                <i class="bi bi-file-earmark-excel me-1"></i> Export
            </a>
            
            <button type="button" class="btn btn-outline-primary rounded-pill px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#importModal">
                <i class="bi bi-file-earmark-arrow-up me-1"></i> Import
            </button>

            <a href="{{ route('siswa.create') }}" class="btn btn-primary rounded-pill px-4 shadow-sm">
                <i class="bi bi-plus-lg me-1"></i> Tambah Siswa
            </a>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-muted small">
                    <tr>
                        <th class="ps-4 py-3">QR CODE / NIS</th>
                        <th>NAMA SISWA</th>
                        <th>KELAS</th>
                        <th>STATUS</th>
                        <th class="text-end pe-4">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($siswas as $s)
                    <tr>
                        <td class="ps-4">
                            <div class="fw-bold text-primary small mb-1">{{ $s->unique_id }}</div>
                            <div class="text-muted" style="font-size: 0.75rem;">NIS: {{ $s->nis }}</div>
                        </td>
                        <td>
                            <div class="fw-bold text-dark text-uppercase" style="font-size: 0.9rem;">{{ $s->nama_siswa }}</div>
                        </td>
                        <td>
                            @if($s->kelas)
                                <span class="badge bg-white text-dark border fw-medium px-3 py-2 rounded-pill shadow-xs">
                                    <i class="bi bi-door-open me-1 text-primary"></i> {{ $s->kelas->nama_kelas }}
                                </span>
                            @else
                                <span class="text-muted small italic">- Tanpa Kelas -</span>
                            @endif
                        </td>
                        <td>
                            @if($s->status == 'aktif')
                                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-2">
                                    <i class="bi bi-check-circle me-1"></i> Aktif
                                </span>
                            @elseif($s->status == 'nonaktif')
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-3 py-2">
                                    <i class="bi bi-exclamation-triangle me-1"></i> Skorsing
                                </span>
                            @else
                                <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill px-3 py-2">
                                    {{ ucfirst($s->status) }}
                                </span>
                            @endif
                        </td>
                        <td class="text-end pe-4">
                            <div class="btn-group shadow-sm rounded-3 overflow-hidden border">
                                <a href="{{ route('siswa.edit', $s->id) }}" class="btn btn-sm btn-white border-0" title="Edit Data">
                                    <i class="bi bi-pencil text-warning"></i>
                                </a>

                                <form action="{{ route('siswa.keluar', $s->id) }}" method="POST" id="form-keluar-{{ $s->id }}">
                                    @csrf
                                    <button type="button" class="btn btn-sm btn-white border-0 border-start" title="Keluarkan Siswa"
                                        onclick="confirmAction('form-keluar-{{ $s->id }}', 'KELUARKAN SISWA', 'Siswa <b>{{ $s->nama_siswa }}</b> akan dinonaktifkan.', 'info', '#0ea5e9')">
                                        <i class="bi bi-box-arrow-right text-info"></i>
                                    </button>
                                </form>

                                <form action="{{ route('siswa.destroy', $s->id) }}" method="POST" id="form-hapus-{{ $s->id }}">
                                    @csrf 
                                    @method('DELETE')
                                    <button type="button" class="btn btn-sm btn-white border-0 border-start" title="Hapus Permanen"
                                        onclick="confirmDelete('form-hapus-{{ $s->id }}', '{{ $s->nama_siswa }}')">
                                        <i class="bi bi-trash text-danger"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5">
                            <i class="bi bi-people display-1 text-muted opacity-25 d-block mb-3"></i>
                            <span class="text-muted italic">Data siswa tidak ditemukan.</span>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($siswas->hasPages())
        <div class="card-footer bg-white border-0 py-4 px-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center">
                <div class="text-muted small mb-3 mb-md-0">
                    Menampilkan <span class="fw-bold text-dark">{{ $siswas->firstItem() }}</span> sampai <span class="fw-bold text-dark">{{ $siswas->lastItem() }}</span> dari <span class="fw-bold text-dark">{{ $siswas->total() }}</span> siswa
                </div>
                <div class="pagination-container">
                    {{ $siswas->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<div class="modal fade" id="importModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 pt-4 px-4">
                <h5 class="modal-title fw-bold text-dark">Import Data Siswa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('siswa.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body px-4">
                    <div class="mb-4 p-3 bg-primary-subtle rounded-3 border border-primary-subtle d-flex align-items-center justify-content-between">
                        <div class="small">
                            <p class="fw-bold mb-0 text-primary">Belum punya template?</p>
                            <span class="text-muted">Gunakan format Excel yang benar.</span>
                        </div>
                        <a href="{{ route('siswa.template') }}" class="btn btn-primary btn-sm rounded-pill shadow-sm">
                            <i class="bi bi-download me-1"></i> Unduh
                        </a>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark">Pilih File Excel/CSV</label>
                        <input type="file" name="file" class="form-control rounded-3 py-2" accept=".xlsx, .xls, .csv" required>
                    </div>

                    <div class="p-3 bg-light rounded-3 border border-dashed text-muted" style="font-size: 0.75rem; line-height: 1.6;">
                        <h6 class="fw-bold small mb-2 text-danger"><i class="bi bi-exclamation-circle me-1"></i> Penting:</h6>
                        <ul class="mb-0 ps-3">
                            <li>Pastikan kolom <b>id_kelas</b> sesuai dengan ID di tabel Kelas.</li>
                            <li>Kolom <b>unique_id</b> boleh dikosongkan (otomatis dibuat).</li>
                            <li>Gunakan baris pertama sebagai judul (Header).</li>
                        </ul>
                    </div>
                </div>
                <div class="modal-footer border-0 pb-4 px-4 pt-3">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow">
                        <i class="bi bi-cloud-arrow-up me-2"></i> Mulai Import
                    </button>
                </div>
            </form>
        </div>
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

    .btn-white { background: #fff; }
    .btn-white:hover { background: #f8f9fa; }
    .shadow-xs { box-shadow: 0 1px 2px rgba(0,0,0,0.05); }
    .italic { font-style: italic; }
</style>
@endsection