@extends('layouts.app')

@section('title', 'Tambah Siswa Baru')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-md-7">
            <div class="d-flex align-items-center mb-4">
                <a href="{{ route('siswa.index') }}" class="btn btn-white shadow-sm rounded-circle me-3">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <div>
                    <h3 class="fw-bold mb-0">Tambah Siswa</h3>
                    <p class="text-muted small mb-0">Daftarkan siswa baru ke sistem EZBorrow</p>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <form action="{{ route('siswa.store') }}" method="POST">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label fw-bold small">Nama Lengkap Siswa</label>
                                <input type="text" name="nama_siswa" class="form-control @error('nama_siswa') is-invalid @enderror" 
                                       placeholder="Contoh: Fiqri Haikal" value="{{ old('nama_siswa') }}" required autofocus>
                                @error('nama_siswa') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold small">NIS (Nomor Induk Siswa)</label>
                                <input type="text" name="nis" class="form-control @error('nis') is-invalid @enderror" 
                                       placeholder="Contoh: 10120795" value="{{ old('nis') }}" required>
                                @error('nis') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold small">Kelas</label>
                                <select name="kelas_id" class="form-select @error('kelas_id') is-invalid @enderror" required>
                                    <option value="" selected disabled>Pilih Kelas...</option>
                                    @foreach($kelas as $k)
                                        <option value="{{ $k->id }}" {{ old('kelas_id') == $k->id ? 'selected' : '' }}>
                                            {{ $k->nama_kelas }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('kelas_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-12">
                                <div class="p-3 bg-light rounded-3 border border-dashed">
                                    <div class="d-flex align-items-center text-primary">
                                        <i class="bi bi-info-circle-fill me-2"></i>
                                        <span class="small fw-bold">Info QR Code</span>
                                    </div>
                                    <p class="small text-muted mb-0 mt-1">
                                        Unique ID untuk QR Code akan dibuat secara otomatis oleh sistem. Anda bisa mencetak kartu siswa setelah data disimpan.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4 opacity-50">

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('siswa.index') }}" class="btn btn-light px-4 rounded-pill">Batal</a>
                            <button type="submit" class="btn btn-primary px-4 rounded-pill fw-bold shadow-sm">
                                <i class="bi bi-save me-2"></i> Simpan Data Siswa
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection