@extends('layouts.app')

@section('title', 'Manajemen Voucher Internet per Kelas')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <div>
                <h3 class="fw-bold text-main mb-1">Manajemen Voucher ISP</h3>
                <p class="text-muted small mb-0">Kelola distribusi kode voucher internet berdasarkan jaringan ISP kelas.</p>
            </div>
            @if($totalVoucher > 0)
            <form action="{{ route('voucher.clearAll') }}" method="POST" id="form-kosongkan-semua">
                @csrf @method('DELETE')
                <button type="button" class="btn btn-danger rounded-pill px-4 shadow-sm fw-bold"
                    onclick="confirmAction('form-kosongkan-semua', 'KOSONGKAN STOK?', 'Apakah Anda yakin ingin menghapus <b>SELURUH</b> stok voucher ({{ $totalVoucher }} kode)? Tindakan ini tidak bisa dibatalkan.', 'warning', '#dc3545')">
                    <i class="bi bi-trash3-fill me-2"></i>Kosongkan Stok
                </button>
            </form>
            @endif
        </div>
    </div>

    <div class="row g-4">
        {{-- Sisi Kiri: Monitor Tabel --}}
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
                <div class="card-header bg-white p-4 border-0">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="fw-bold mb-0 text-main">Monitor Persediaan</h5>
                        <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-2 border border-primary-subtle shadow-xs">
                            Total Stok: {{ $totalVoucher }} Kode
                        </span>
                    </div>

                    <div class="p-3 bg-light rounded-4 border border-dashed">
                        <form action="{{ route('voucher.index') }}" method="GET" class="row g-2">
                            <div class="col-md-9">
                                <select name="kelas_id" class="form-select rounded-pill shadow-xs border-0" onchange="this.form.submit()">
                                    <option value="">-- Semua Kelas --</option>
                                    @foreach($kelas as $k)
                                        <option value="{{ $k->id }}" {{ request('kelas_id') == $k->id ? 'selected' : '' }}>
                                            {{ $k->nama_kelas }} (Tersedia: {{ $k->vouchers_count ?? 0 }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <a href="{{ route('voucher.index') }}" class="btn btn-white w-100 rounded-pill border shadow-xs">Reset</a>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light small text-muted">
                            <tr>
                                <th class="ps-4 py-3" width="50">NO</th>
                                <th>KODE VOUCHER</th>
                                <th>AKSES KELAS (ISP)</th>
                                <th class="text-end pe-4">AKSI</th>
                            </tr>
                        </thead>
                        <tbody class="border-0">
                            @forelse($vouchers as $index => $v)
                            <tr>
                                <td class="ps-4">
                                    <span class="text-muted small">{{ ($vouchers->currentPage() - 1) * $vouchers->perPage() + $loop->iteration }}</span>
                                </td>
                                <td><code class="fw-bold text-primary fs-6 px-2 py-1 bg-primary-subtle rounded border border-primary-subtle shadow-xs">{{ $v->kode_voucher }}</code></td>
                                <td>
                                    @forelse($v->kelas as $itemKelas)
                                        <span class="badge bg-info-subtle text-info border border-info-subtle rounded-pill mb-1">
                                            <i class="bi bi-broadcast me-1"></i> {{ $itemKelas->nama_kelas }}
                                        </span>
                                    @empty
                                        <span class="text-muted small italic">Tidak ada kelas</span>
                                    @endforelse
                                </td>
                                <td class="text-end pe-4">
                                    <form action="{{ route('voucher.destroy', $v->id) }}" method="POST" id="form-hapus-v-{{ $v->id }}">
                                        @csrf @method('DELETE')
                                        <button type="button" class="btn btn-sm btn-outline-danger border-0 rounded-circle"
                                            onclick="confirmDelete('form-hapus-v-{{ $v->id }}', 'Voucher {{ $v->kode_voucher }}')">
                                            <i class="bi bi-trash-fill"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-5">
                                    <i class="bi bi-ticket-perforated display-4 text-muted opacity-25 d-block mb-2"></i>
                                    <p class="text-muted mb-0 italic">Belum ada stok voucher.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Bagian Pagination: Diselaraskan dengan Chromebook/Guru/Kelas --}}
                @if($vouchers->hasPages())
                <div class="card-footer bg-card border-0 py-4 px-4">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                        <div class="text-muted small order-2 order-md-1">
                            Menampilkan <b>{{ $vouchers->firstItem() }}</b> - <b>{{ $vouchers->lastItem() }}</b> dari <b>{{ $vouchers->total() }}</b> voucher
                        </div>
                        <div class="pagination-container order-1 order-md-2">
                            {{ $vouchers->links('pagination::bootstrap-5') }}
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>

        {{-- Sisi Kanan: Form Tambah --}}
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                <div class="bg-primary p-4 text-white">
                    <h5 class="fw-bold mb-1"><i class="bi bi-plus-square-fill me-2"></i>Tambah Stok Baru</h5>
                    <p class="small opacity-75 mb-0">Wajib pilih kelas sebelum menyimpan kode voucher.</p>
                </div>
                <div class="card-body p-4 bg-card">
                    <form action="{{ route('voucher.store') }}" method="POST" enctype="multipart/form-data" id="form-tambah-voucher">
                        @csrf
                        
                        <div class="mb-4">
                            <label class="form-label small fw-bold text-main mb-2">
                                <span class="badge bg-primary rounded-circle me-1" style="width:20px; height:20px; padding:2px">1</span> 
                                Pilih Kelas (Target ISP)
                            </label>
                            <div class="p-3 bg-light rounded-4 border shadow-xs" style="max-height: 180px; overflow-y: auto;">
                                @foreach($kelas as $k)
                                <div class="form-check custom-checkbox mb-2">
                                    <input class="form-check-input" type="checkbox" name="kelas_ids[]" value="{{ $k->id }}" id="kelas_{{ $k->id }}">
                                    <label class="form-check-label small d-flex justify-content-between align-items-center w-100" for="kelas_{{ $k->id }}">
                                        <span class="fw-bold text-main">{{ $k->nama_kelas }}</span>
                                        <span class="text-muted small italic">Stok: {{ $k->vouchers_count ?? 0 }}</span>
                                    </label>
                                </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label small fw-bold text-main mb-3">
                                <span class="badge bg-primary rounded-circle me-1" style="width:20px; height:20px; padding:2px">2</span> 
                                Pilih Metode Input
                            </label>
                            
                            <div class="nav-method-wrapper p-1 bg-light rounded-pill border mb-3">
                                <ul class="nav nav-pills nav-justified" id="pills-tab" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link active rounded-pill py-2" data-bs-toggle="pill" data-bs-target="#pills-bulk" type="button">
                                            <i class="bi bi-textarea-t me-2"></i>Teks Massal
                                        </button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link rounded-pill py-2" data-bs-toggle="pill" data-bs-target="#pills-excel" type="button">
                                            <i class="bi bi-file-earmark-excel me-2"></i>Excel
                                        </button>
                                    </li>
                                </ul>
                            </div>
                            
                            <div class="tab-content" id="pills-tabContent">
                                <div class="tab-pane fade show active" id="pills-bulk">
                                    <textarea name="bulk_codes" class="form-control border-0 bg-light rounded-4 p-3 shadow-xs" rows="6" 
                                        placeholder="Masukkan Voucher di sini (satu voucher per baris)..."></textarea>
                                </div>
                                <div class="tab-pane fade" id="pills-excel">
                                    <div class="p-4 border border-2 border-dashed rounded-4 bg-light text-center">
                                        <i class="bi bi-cloud-arrow-up display-6 text-muted mb-2"></i>
                                        <input type="file" name="file_voucher" class="form-control form-control-sm shadow-xs" accept=".xlsx,.xls,.csv">
                                        <p class="small text-muted mt-2 mb-0">Kolom A: Kode Voucher</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <button type="button" onclick="validateVoucherForm()" class="btn btn-primary w-100 rounded-pill fw-bold py-3 shadow border-0">
                            <i class="bi bi-cloud-plus-fill me-2"></i>Simpan Stok Voucher
                        </button>
                    </form>
                </div>
            </div>

            <div class="p-4 bg-card rounded-4 border-start border-4 border-primary shadow-sm">
                <h6 class="fw-bold mb-2 small text-main"><i class="bi bi-shield-check me-2 text-primary"></i>Info Sistem</h6>
                <p class="small text-muted mb-0" style="font-size: 0.75rem;">Sistem otomatis menyaring kode duplikat. Satu kode unik dapat dipetakan ke berbagai kelas pilihan.</p>
            </div>
        </div>
    </div>
</div>

<style>
    /* Mengikuti gaya seragam sistem (Chromebook/Guru/Siswa) */
    .text-main { color: var(--text-main) !important; }
    .bg-card { background-color: var(--card-bg) !important; }

    .pagination { margin-bottom: 0; gap: 4px; flex-wrap: wrap; justify-content: center; }
    .page-item .page-link { border-radius: 8px !important; border: 1px solid #eef2f7; color: #64748b; font-weight: 600; font-size: 0.8rem; padding: 6px 12px; transition: all 0.2s; }
    .page-item.active .page-link { background-color: var(--primary-color) !important; border-color: var(--primary-color) !important; color: #fff !important; }
    .page-item:not(.active) .page-link:hover { background-color: #f8f9fa; border-color: var(--primary-color); color: var(--primary-color); }

    .nav-pills .nav-link { color: #64748b; font-weight: 600; font-size: 0.8rem; border: none; background: transparent; }
    .nav-pills .nav-link.active { background-color: var(--primary-color) !important; color: #fff !important; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
    
    .btn-white { background: var(--card-bg); color: var(--text-main); }
    .shadow-xs { box-shadow: 0 1px 2px rgba(0,0,0,0.05); }
    code { font-family: 'Courier New', Courier, monospace; letter-spacing: 0.5px; }
    .italic { font-style: italic; }

    /* Custom scrollbar */
    .table-responsive::-webkit-scrollbar { height: 6px; }
    .table-responsive::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }

    @media (max-width: 576px) {
        .card { border-radius: 1rem !important; }
    }
</style>

@section('js')
<script>
    function validateVoucherForm() {
        const selectedKelas = document.querySelectorAll('input[name="kelas_ids[]"]:checked');
        const bulkCodes = document.querySelector('textarea[name="bulk_codes"]').value.trim();
        const fileInput = document.querySelector('input[name="file_voucher"]').files.length;

        if (selectedKelas.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Pilih Kelas!',
                text: 'Mohon pilih minimal satu kelas tujuan voucher sebelum menyimpan.',
                confirmButtonColor: '#0d6efd',
                customClass: { popup: 'rounded-4' }
            });
            return;
        }

        if (bulkCodes === "" && fileInput === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Data Kosong!',
                text: 'Silakan isi kode voucher di kotak teks atau unggah file Excel.',
                confirmButtonColor: '#0d6efd',
                customClass: { popup: 'rounded-4' }
            });
            return;
        }

        Swal.fire({
            title: 'Sedang Menyimpan...',
            text: 'Mohon tunggu, sedang memproses data voucher.',
            allowOutsideClick: false,
            didOpen: () => { Swal.showLoading(); }
        });

        document.getElementById('form-tambah-voucher').submit();
    }

    function confirmAction(formId, title, message, icon, confirmColor) {
        Swal.fire({
            title: title,
            html: message,
            icon: icon,
            showCancelButton: true,
            confirmButtonColor: confirmColor,
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Ya, Lakukan!',
            cancelButtonText: 'Batal',
            reverseButtons: true,
            customClass: { popup: 'rounded-4' }
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById(formId).submit();
            }
        });
    }

    function confirmDelete(formId, itemName) {
        confirmAction(formId, 'Hapus Data?', `Apakah Anda yakin ingin menghapus <b>${itemName}</b>?`, 'warning', '#ef4444');
    }
</script>
@endsection
@endsection