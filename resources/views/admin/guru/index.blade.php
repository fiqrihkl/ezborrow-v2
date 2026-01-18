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
                                <button type="button" class="btn btn-sm btn-white border-0 border-start" 
                                        onclick="confirmDeleteGuru('{{ $guru->id }}', '{{ $guru->nama_guru }}')" title="Hapus">
                                    <i class="bi bi-trash3 text-danger"></i>
                                </button>
                            </div>

                            {{-- Form Hapus Tersembunyi --}}
                            <form action="{{ route('guru.destroy', $guru->id) }}" method="POST" id="form-hapus-guru-{{ $guru->id }}" style="display:none;">
                                @csrf 
                                @method('DELETE')
                            </form>
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
    .text-main { color: var(--text-main) !important; }
    .bg-card { background-color: var(--card-bg) !important; }
    .pagination { margin-bottom: 0; gap: 4px; flex-wrap: wrap; justify-content: center; }
    .page-item .page-link { border-radius: 8px !important; border: 1px solid #eef2f7; color: #64748b; font-weight: 600; font-size: 0.8rem; padding: 6px 12px; }
    .page-item.active .page-link { background-color: var(--primary-color) !important; border-color: var(--primary-color) !important; color: #fff !important; }
    .btn-white { background: var(--card-bg); color: var(--text-main); }
    .italic { font-style: italic; }
</style>

{{-- SweetAlert2 Notifikasi & Konfirmasi --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // 1. Notifikasi Sukses/Error dari Session
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Berhasil',
            text: "{{ session('success') }}",
            timer: 3000,
            showConfirmButton: false
        });
    @endif

    @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Gagal',
            text: "{{ session('error') }}",
        });
    @endif

    // 2. Fungsi Konfirmasi Hapus
    function confirmDeleteGuru(id, name) {
        Swal.fire({
            title: 'Yakin Hapus Data Guru?',
            html: `Apakah Anda yakin ingin menghapus <b>${name}</b>? <br><br> 
                   <div class="p-3 bg-light rounded-3 text-start small">
                    <i class="bi bi-exclamation-triangle-fill text-warning me-2"></i> 
                    <b>Dampak penghapusan:</b><br>
                    1. Jabatan Wali Kelas akan dikosongkan.<br>
                    2. Seluruh riwayat peminjaman terkait guru ini akan <b>dihapus permanen</b>.
                   </div>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444', 
            cancelButtonColor: '#64748b', 
            confirmButtonText: 'Ya, Tetap Hapus',
            cancelButtonText: 'Tidak, Batal',
            reverseButtons: true,
            customClass: {
                popup: 'rounded-4 border-0'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                // Tampilkan loading sebentar sebelum submit
                Swal.fire({
                    title: 'Memproses...',
                    didOpen: () => { Swal.showLoading() },
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false
                });
                document.getElementById('form-hapus-guru-' + id).submit();
            }
        });
    }
</script>
@endsection