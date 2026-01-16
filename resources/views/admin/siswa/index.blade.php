@extends('layouts.app')

@section('title', 'Manajemen Siswa')

@section('content')
<div class="container-fluid py-3 py-md-4">
    {{-- Header: Menumpuk di Mobile, Berjajar di Desktop --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h3 class="fw-bold mb-1 text-main">Data Siswa</h3>
            <p class="text-muted small mb-0">Kelola data siswa aktif dan pantau status peminjaman</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('siswa.export', ['search' => request('search'), 'kelas_id' => request('kelas_id')]) }}" 
               class="btn btn-outline-success rounded-pill px-3 shadow-sm flex-fill flex-md-grow-0">
                <i class="bi bi-file-earmark-excel me-1"></i> Export
            </a>
            <button type="button" class="btn btn-outline-primary rounded-pill px-3 shadow-sm flex-fill flex-md-grow-0" data-bs-toggle="modal" data-bs-target="#importModal">
                <i class="bi bi-file-earmark-arrow-up me-1"></i> Import
            </button>
            <a href="{{ route('siswa.create') }}" class="btn btn-primary rounded-pill px-4 shadow-sm flex-fill flex-md-grow-0">
                <i class="bi bi-plus-lg me-1"></i> <span class="d-none d-sm-inline">Tambah</span> Siswa
            </a>
        </div>
    </div>

    {{-- Form Filter: Menggunakan Grid yang adaptif --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-3 p-md-4">
            <form action="{{ route('siswa.index') }}" method="GET" class="row g-3">
                <div class="col-12 col-lg-4">
                    <div class="input-group shadow-xs">
                        <span class="input-group-text bg-card border-end-0 rounded-start-pill">
                            <i class="bi bi-search text-muted"></i>
                        </span>
                        <input type="text" name="search" class="form-control bg-card border-start-0 rounded-end-pill" 
                            placeholder="Cari Nama atau NIS..." value="{{ request('search') }}">
                    </div>
                </div>

                <div class="col-12 col-sm-6 col-lg-3">
                    <select name="kelas_id" class="form-select rounded-pill bg-card shadow-xs">
                        <option value="">Semua Kelas</option>
                        @foreach($kelases as $k)
                            <option value="{{ $k->id }}" {{ request('kelas_id') == $k->id ? 'selected' : '' }}>
                                {{ $k->nama_kelas }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12 col-sm-6 col-lg-3">
                    <select name="status" class="form-select rounded-pill bg-card shadow-xs">
                        <option value="">Status (Semua)</option>
                        <option value="aktif" {{ request('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="nonaktif" {{ request('status') == 'nonaktif' ? 'selected' : '' }}>Skorsing</option>
                        <option value="alumni" {{ request('status') == 'alumni' ? 'selected' : '' }}>Lulus (Alumni)</option>
                        <option value="keluar" {{ request('status') == 'keluar' ? 'selected' : '' }}>Keluar / Pindah</option>
                    </select>
                </div>

                <div class="col-12 col-lg-2 d-flex gap-2">
                    <button type="submit" class="btn btn-dark rounded-pill w-100 shadow-sm">Filter</button>
                    @if(request('search') || request('kelas_id') || request('status'))
                        <a href="{{ route('siswa.index') }}" class="btn btn-light rounded-pill px-3 shadow-sm border">
                            <i class="bi bi-arrow-clockwise"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    {{-- Tabel: Responsive dengan min-width agar tidak gepeng di mobile --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="min-width: 800px;">
                <thead class="bg-light text-muted small">
                    <tr>
                        <th class="ps-4 py-3" width="50">NO</th>
                        <th>QR CODE / NIS</th>
                        <th>NAMA SISWA</th>
                        <th>KELAS</th>
                        <th>STATUS</th>
                        <th class="text-end pe-4">AKSI</th>
                    </tr>
                </thead>
                <tbody class="border-0">
                    @forelse($siswas as $index => $s)
                    <tr>
                        <td class="ps-4">
                            <span class="text-muted small">{{ ($siswas->currentPage() - 1) * $siswas->perPage() + $loop->iteration }}</span>
                        </td>
                        <td>
                            <div class="fw-bold text-primary small mb-0">{{ $s->unique_id }}</div>
                            <div class="text-muted" style="font-size: 0.7rem;">NIS: {{ $s->nis }}</div>
                        </td>
                        <td>
                            <div class="fw-bold text-main text-uppercase" style="font-size: 0.85rem;">{{ $s->nama_siswa }}</div>
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
                            @php
                                $statusClasses = [
                                    'aktif' => 'bg-success-subtle text-success border-success-subtle',
                                    'nonaktif' => 'bg-danger-subtle text-danger border-danger-subtle',
                                    'alumni' => 'bg-primary-subtle text-primary border-primary-subtle',
                                    'keluar' => 'bg-secondary-subtle text-secondary border-secondary-subtle'
                                ];
                                $statusIcons = [
                                    'aktif' => 'bi-check-circle',
                                    'nonaktif' => 'bi-exclamation-triangle',
                                    'alumni' => 'bi-mortarboard',
                                    'keluar' => 'bi-door-closed'
                                ];
                                $label = ($s->status == 'nonaktif') ? 'Skorsing' : (($s->status == 'alumni') ? 'Alumni' : ucfirst($s->status));
                            @endphp
                            <span class="badge {{ $statusClasses[$s->status] ?? $statusClasses['keluar'] }} border rounded-pill px-3 py-2">
                                <i class="bi {{ $statusIcons[$s->status] ?? 'bi-info-circle' }} me-1"></i> {{ $label }}
                            </span>
                        </td>
                        <td class="text-end pe-4">
                            <div class="btn-group shadow-sm rounded-3 overflow-hidden border">
                                <a href="{{ route('siswa.edit', $s->id) }}" class="btn btn-sm btn-white border-0" title="Edit Data">
                                    <i class="bi bi-pencil text-warning"></i>
                                </a>
                                <form action="{{ route('siswa.keluar', $s->id) }}" method="POST" id="form-keluar-{{ $s->id }}">
                                    @csrf
                                    <button type="button" class="btn btn-sm btn-white border-0 border-start" 
                                        onclick="confirmAction('form-keluar-{{ $s->id }}', 'KELUARKAN SISWA', 'Siswa <b>{{ $s->nama_siswa }}</b> akan dinonaktifkan.', 'info', '#0ea5e9')">
                                        <i class="bi bi-box-arrow-right text-info"></i>
                                    </button>
                                </form>
                                <form action="{{ route('siswa.destroy', $s->id) }}" method="POST" id="form-hapus-{{ $s->id }}">
                                    @csrf @method('DELETE')
                                    <button type="button" class="btn btn-sm btn-white border-0 border-start" 
                                        onclick="confirmDelete('form-hapus-{{ $s->id }}', '{{ $s->nama_siswa }}')">
                                        <i class="bi bi-trash text-danger"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <i class="bi bi-search display-1 text-muted opacity-25 d-block mb-3"></i>
                            <span class="text-muted italic">Data siswa tidak ditemukan.</span>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        {{-- Pagination: Menumpuk di mobile --}}
        @if($siswas->hasPages())
        <div class="card-footer bg-card border-0 py-4 px-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                <div class="text-muted small order-2 order-md-1">
                    Menampilkan <b>{{ $siswas->firstItem() }}</b> - <b>{{ $siswas->lastItem() }}</b> dari <b>{{ $siswas->total() }}</b> siswa
                </div>
                <div class="pagination-container order-1 order-md-2">
                    {{ $siswas->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

{{-- Modal Import: Dibuat responsif --}}
<div class="modal fade" id="importModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-fullscreen-sm-down">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 pt-4 px-4">
                <h5 class="modal-title fw-bold text-main">Import Data Siswa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('siswa.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body px-4">
                    <div class="mb-4 p-3 bg-primary-subtle rounded-3 border border-primary-subtle d-flex align-items-center justify-content-between">
                        <div class="small">
                            <p class="fw-bold mb-0 text-primary">Belum punya template?</p>
                            <span class="text-muted">Gunakan format Excel standar.</span>
                        </div>
                        <a href="{{ route('siswa.template') }}" class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm">
                            <i class="bi bi-download"></i>
                        </a>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-main">Pilih Kelas Tujuan</label>
                        <select name="kelas_id" class="form-select rounded-3 py-2 bg-card" required>
                            <option value="" selected disabled>-- Pilih Kelas --</option>
                            @foreach($kelases as $k)
                                <option value="{{ $k->id }}">{{ $k->nama_kelas }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-main">Pilih File Excel/CSV</label>
                        <input type="file" name="file" class="form-control rounded-3 py-2 bg-card" accept=".xlsx, .xls, .csv" required>
                    </div>
                </div>
                <div class="modal-footer border-0 pb-4 px-4 pt-3">
                    <button type="button" class="btn btn-light rounded-pill px-4 border" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow">
                        <i class="bi bi-cloud-arrow-up me-2"></i> Import
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    /* Dukungan Warna Dinamis (Light/Dark Mode) */
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
        .modal-fullscreen-sm-down { margin: 0; }
        .modal-fullscreen-sm-down .modal-content { border-radius: 0; min-height: 100vh; }
    }
</style>
@endsection