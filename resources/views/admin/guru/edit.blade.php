@extends('layouts.app')

@section('title', 'Edit Data Guru')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="d-flex align-items-center mb-4">
                <a href="{{ route('guru.index') }}" class="btn btn-white shadow-sm rounded-circle me-3">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <div>
                    <h3 class="fw-bold mb-0">Edit Guru</h3>
                    <p class="text-muted small mb-0">Memperbarui informasi: {{ $guru->nama_guru }}</p>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <form action="{{ route('guru.update', $guru->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold small">NIP (Nomor Induk Pegawai) <span class="text-muted fw-normal">(Opsional)</span></label>
                            {{-- Atribut 'required' dihapus untuk membuat NIP menjadi opsional --}}
                            <input type="text" name="nip" 
                                   class="form-control @error('nip') is-invalid @enderror" 
                                   value="{{ old('nip', $guru->nip) }}" 
                                   placeholder="Masukkan NIP (kosongkan jika tidak ada)...">
                            @error('nip') 
                                <div class="invalid-feedback">{{ $message }}</div> 
                            @enderror
                            <small class="text-muted">Jika diisi, NIP harus unik dan tidak boleh sama dengan guru lain.</small>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold small">Nama Lengkap Guru</label>
                            <input type="text" name="nama_guru" 
                                   class="form-control @error('nama_guru') is-invalid @enderror" 
                                   value="{{ old('nama_guru', $guru->nama_guru) }}" 
                                   placeholder="Contoh: Drs. Fiqri Haikal, M.Kom" required>
                            @error('nama_guru') 
                                <div class="invalid-feedback">{{ $message }}</div> 
                            @enderror
                        </div>

                        <hr class="my-4 opacity-50">

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('guru.index') }}" class="btn btn-light px-4 rounded-pill">Batal</a>
                            <button type="submit" class="btn btn-warning px-4 rounded-pill fw-bold shadow-sm">
                                <i class="bi bi-pencil-square me-2"></i> Perbarui Data Guru
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="mt-4 p-3 bg-light rounded-4 border border-dashed border-secondary">
                <div class="d-flex">
                    <i class="bi bi-info-circle text-primary me-3 fs-4"></i>
                    <small class="text-muted">
                        Perubahan pada nama guru akan otomatis terupdate pada riwayat peminjaman yang terkait dengan guru ini.
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection