    @extends('layouts.app')

    @section('title', 'Pengaturan Sistem')

    @section('content')
    <div class="container-fluid py-4">
        <div class="mb-4">
            <h3 class="fw-bold mb-1"><i class="bi bi-gear-fill text-primary me-2"></i>Pengaturan Sistem</h3>
            <p class="text-muted small mb-0">Kelola profil resmi sekolah dan personalisasi aplikasi.</p>
        </div>

        <form action="{{ route('settings.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-header bg-white border-0 pt-4 px-4">
                            <h5 class="fw-bold mb-0"><i class="bi bi-building me-2 text-primary"></i>Profil Resmi Sekolah</h5>
                            <p class="text-muted small mb-0">Data ini akan otomatis muncul pada Kop Surat/Header laporan cetak.</p>
                            <hr class="mt-3 mb-0">
                        </div>
                        <div class="card-body p-4">
                            <div class="row mb-4 align-items-center">
                                <div class="col-md-3 text-center mb-3 mb-md-0">
                                    <div class="position-relative d-inline-block">
                                        <img src="{{ isset($settings['school_logo']) ? asset($settings['school_logo']) : 'https://via.placeholder.com/150' }}" 
                                            class="img-thumbnail shadow-sm" 
                                            style="width: 130px; height: 130px; object-fit: contain; border-radius: 15px; border: 2px solid #f8f9fa;">
                                    </div>
                                </div>
                                <div class="col-md-9">
                                    <label class="form-label small fw-bold">Logo Resmi Sekolah</label>
                                    <input type="file" name="logo" class="form-control rounded-pill mb-2">
                                    <small class="text-muted"><i class="bi bi-info-circle me-1"></i> Gunakan file PNG transparan untuk hasil terbaik di cetakan laporan.</small>
                                </div>
                            </div>

                            <hr class="mb-4 opacity-50">

                            <div class="row g-3">
                                <div class="col-md-8">
                                    <label class="form-label small fw-bold">Nama Lengkap Sekolah</label>
                                    <input type="text" name="school_name" class="form-control rounded-3" value="{{ $settings['school_name'] ?? '' }}" placeholder="Contoh: SD Negeri 01 Makassar">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold">NPSN</label>
                                    <input type="text" name="school_npsn" class="form-control rounded-3" value="{{ $settings['school_npsn'] ?? '' }}" placeholder="Nomor Pokok Sekolah">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Email Instansi</label>
                                    <input type="email" name="school_email" class="form-control rounded-3" value="{{ $settings['school_email'] ?? '' }}" placeholder="kontak@sekolah.sch.id">
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
                                <small class="text-muted" style="font-size: 0.7rem;">Nama yang muncul di judul browser dan navbar.</small>
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
                                    <label class="form-label small fw-bold">Warna Utama</label>
                                    <input type="color" name="primary_color" class="form-control form-control-color w-100 rounded-3 shadow-sm" value="{{ $settings['primary_color'] ?? '#0d6efd' }}">
                                </div>
                                <div class="col-6 text-center">
                                    <label class="form-label small fw-bold">Warna Aksen</label>
                                    <input type="color" name="secondary_color" class="form-control form-control-color w-100 rounded-3 shadow-sm" value="{{ $settings['secondary_color'] ?? '#6c757d' }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-4 text-end">
                <button type="submit" class="btn btn-primary rounded-pill px-5 py-2 fw-bold shadow">
                    <i class="bi bi-check-circle me-2"></i>Simpan Perubahan
                </button>
            </div>
        </form>
    </div>

    <style>
        .form-control-color { height: 45px; cursor: pointer; }
        .img-thumbnail { background-color: #fff; transition: transform .2s; }
        .img-thumbnail:hover { transform: scale(1.05); }
        .card { transition: all 0.3s ease; }
    </style>
    @endsection