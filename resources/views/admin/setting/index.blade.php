@extends('layouts.app')

@section('title', 'Pengaturan Sistem')

@section('content')
<div class="container-fluid py-4">
    <div class="mb-4 d-flex justify-content-between align-items-center">
        <div>
            <h3 class="fw-bold mb-1"><i class="bi bi-gear-fill text-primary me-2"></i>Pengaturan Sistem</h3>
            <p class="text-muted small mb-0">Kelola profil resmi sekolah dan personalisasi aplikasi.</p>
        </div>
    </div>

    <form action="{{ route('settings.update') }}" method="POST" enctype="multipart/form-data" id="settingsForm">
        @csrf
        <div class="row g-4">
            {{-- Bagian Kiri: Profil Sekolah --}}
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-header bg-white border-0 pt-4 px-4">
                        <h5 class="fw-bold mb-0"><i class="bi bi-building me-2 text-primary"></i>Profil Resmi Sekolah</h5>
                        <p class="text-muted small mb-0">Data ini akan otomatis muncul pada Kop Surat/Header laporan cetak.</p>
                        <hr class="mt-3 mb-0">
                    </div>
                    <div class="card-body p-4">
                        <div class="row mb-4 align-items-center">
                            <div class="col-md-4 text-center mb-3 mb-md-0">
                                <div class="logo-preview-wrapper position-relative d-inline-block">
                                    @if(isset($settings['school_logo']) && $settings['school_logo'] != '')
                                        <img src="{{ asset($settings['school_logo']) }}" 
                                             id="logoPreview"
                                             class="img-thumbnail shadow-sm p-2" 
                                             style="width: 160px; height: 160px; object-fit: contain; border-radius: 20px; border: 2px solid #f8f9fa;">
                                        
                                        {{-- Tombol Hapus Logo --}}
                                        <button type="button" 
                                                onclick="removeLogo()"
                                                class="btn btn-danger btn-sm position-absolute top-0 end-0 rounded-circle shadow"
                                                title="Hapus Logo"
                                                style="width: 30px; height: 30px; padding: 0; transform: translate(30%, -30%);">
                                            <i class="bi bi-x"></i>
                                        </button>
                                        {{-- Input hidden untuk tanda hapus logo --}}
                                        <input type="hidden" name="remove_logo" id="removeLogoInput" value="0">
                                    @else
                                        {{-- Tampilan Jika Logo Kosong --}}
                                        <div id="emptyLogoPlaceholder" 
                                             class="d-flex flex-column align-items-center justify-content-center bg-light border border-dashed rounded-4"
                                             style="width: 160px; height: 160px; border-width: 2px !important; color: #adb5bd;">
                                            <i class="bi bi-cloud-arrow-up fs-1 mb-2"></i>
                                            <span style="font-size: 0.7rem;" class="fw-bold text-uppercase">Upload Logo</span>
                                        </div>
                                        <img id="logoPreview" class="d-none img-thumbnail shadow-sm p-2" style="width: 160px; height: 160px; object-fit: contain; border-radius: 20px;">
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-8">
                                <label class="form-label small fw-bold">Update Logo Sekolah</label>
                                <div class="input-group">
                                    <input type="file" name="logo" id="logoInput" class="form-control rounded-pill shadow-xs" accept="image/*">
                                </div>
                                <div class="mt-2">
                                    <small class="text-muted d-block small"><i class="bi bi-info-circle me-1"></i> Format: PNG/JPG. Rekomendasi PNG transparan.</small>
                                    <small class="text-muted d-block small"><i class="bi bi-aspect-ratio me-1"></i> Ukuran ideal 512x512 piksel.</small>
                                </div>
                            </div>
                        </div>

                        <hr class="mb-4 opacity-50">

                        <div class="row g-3">
                            <div class="col-md-8">
                                <label class="form-label small fw-bold">Nama Lengkap Sekolah</label>
                                <input type="text" name="school_name" class="form-control rounded-3" value="{{ $settings['school_name'] ?? '' }}" placeholder="Contoh: SMP Negeri 1 Biau">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">NPSN</label>
                                <input type="text" name="school_npsn" class="form-control rounded-3" value="{{ $settings['school_npsn'] ?? '' }}" placeholder="Nomor Pokok Sekolah">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Email Instansi</label>
                                <input type="email" name="school_email" class="form-control rounded-3" value="{{ $settings['school_email'] ?? '' }}" placeholder="email@sekolah.sch.id">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">No. Telepon</label>
                                <input type="text" name="school_phone" class="form-control rounded-3" value="{{ $settings['school_phone'] ?? '' }}" placeholder="0411-xxxxxx">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label small fw-bold">Alamat Lengkap (Untuk Kop Surat)</label>
                                <textarea name="school_address" class="form-control rounded-3" rows="3" placeholder="Sertakan jalan, kecamatan, kota, dan kode pos">{{ $settings['school_address'] ?? '' }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Bagian Kanan: Branding & Tema --}}
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-header bg-white border-0 pt-4 px-4">
                        <h5 class="fw-bold mb-0"><i class="bi bi-laptop me-2 text-primary"></i>Branding Sistem</h5>
                        <hr class="mt-3 mb-0">
                    </div>
                    <div class="card-body p-4">
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Nama Aplikasi</label>
                            <input type="text" name="app_name" class="form-control rounded-3" value="{{ $settings['app_name'] ?? 'EZBorrow' }}">
                            <small class="text-muted" style="font-size: 0.7rem;">Muncul di judul browser dan navbar.</small>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-header bg-white border-0 pt-4 px-4">
                        <h5 class="fw-bold mb-0"><i class="bi bi-palette me-2 text-primary"></i>Tema Warna</h5>
                        <hr class="mt-3 mb-0">
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-6 text-center">
                                <label class="form-label small fw-bold">Utama</label>
                                <input type="color" name="primary_color" class="form-control form-control-color w-100 rounded-3 shadow-sm" value="{{ $settings['primary_color'] ?? '#0d6efd' }}">
                            </div>
                            <div class="col-6 text-center">
                                <label class="form-label small fw-bold">Aksen</label>
                                <input type="color" name="secondary_color" class="form-control form-control-color w-100 rounded-3 shadow-sm" value="{{ $settings['secondary_color'] ?? '#6c757d' }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-4 text-end pb-5">
            <button type="submit" class="btn btn-primary rounded-pill px-5 py-2 fw-bold shadow">
                <i class="bi bi-check-circle me-2"></i>Simpan Perubahan
            </button>
        </div>
    </form>
</div>

<style>
    .form-control-color { height: 45px; cursor: pointer; border: 2px solid #f8f9fa; }
    .img-thumbnail { background-color: #fff; transition: all .3s ease; }
    .img-thumbnail:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important; }
    .card { transition: all 0.3s ease; border: 1px solid rgba(0,0,0,0.03); }
    .shadow-xs { box-shadow: 0 2px 4px rgba(0,0,0,0.02); }
    .border-dashed { border-style: dashed !important; }
    
    /* Animasi sederhana untuk tombol hapus */
    .logo-preview-wrapper .btn-danger {
        opacity: 0;
        transition: opacity 0.2s ease;
    }
    .logo-preview-wrapper:hover .btn-danger {
        opacity: 1;
    }
</style>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Preview Gambar sebelum upload
    logoInput.onchange = evt => {
        const [file] = logoInput.files;
        if (file) {
            const preview = document.getElementById('logoPreview');
            const placeholder = document.getElementById('emptyLogoPlaceholder');
            
            preview.src = URL.createObjectURL(file);
            preview.classList.remove('d-none');
            if(placeholder) placeholder.classList.add('d-none');
            
            // Reset input remove jika user memilih file baru
            const removeInput = document.getElementById('removeLogoInput');
            if(removeInput) removeInput.value = "0";
        }
    }

    // Fungsi Hapus Logo
    function removeLogo() {
        Swal.fire({
            title: 'Hapus Logo?',
            text: "Logo akan dihapus setelah Anda mengklik tombol Simpan Perubahan.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                // Sembunyikan gambar dan tampilkan placeholder
                document.getElementById('logoPreview').classList.add('d-none');
                
                // Tambahkan placeholder secara dinamis jika belum ada
                if(!document.getElementById('emptyLogoPlaceholder')) {
                    const wrapper = document.querySelector('.logo-preview-wrapper');
                    const placeholder = document.createElement('div');
                    placeholder.id = "emptyLogoPlaceholder";
                    placeholder.className = "d-flex flex-column align-items-center justify-content-center bg-light border border-dashed rounded-4";
                    placeholder.style.width = "160px";
                    placeholder.style.height = "160px";
                    placeholder.style.borderWidth = "2px !important";
                    placeholder.style.color = "#adb5bd";
                    placeholder.innerHTML = '<i class="bi bi-cloud-arrow-up fs-1 mb-2"></i><span style="font-size: 0.7rem;" class="fw-bold text-uppercase">Upload Logo</span>';
                    wrapper.appendChild(placeholder);
                } else {
                    document.getElementById('emptyLogoPlaceholder').classList.remove('d-none');
                }

                // Tandai input hidden untuk dihapus di sisi server
                document.getElementById('removeLogoInput').value = "1";
                // Hilangkan tombol hapus (X) sementara
                document.querySelector('.logo-preview-wrapper .btn-danger').classList.add('d-none');
                
                Swal.fire('Ditandai!', 'Logo ditandai untuk dihapus. Jangan lupa klik Simpan.', 'success');
            }
        })
    }
</script>
@endsection