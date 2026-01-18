@extends('layouts.app')

@section('title', 'Tambah Guru')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="d-flex align-items-center mb-4">
                <a href="{{ route('guru.index') }}" class="btn btn-white shadow-sm rounded-circle me-3"><i class="bi bi-arrow-left"></i></a>
                <h3 class="fw-bold mb-0">Tambah Guru</h3>
            </div>

            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <form action="{{ route('guru.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-bold small">NIP (Nomor Induk Pegawai) <span class="text-muted fw-normal">(Opsional)</span></label>
                            {{-- Atribut 'required' telah dihapus di bawah ini --}}
                            <input type="text" name="nip" class="form-control @error('nip') is-invalid @enderror" placeholder="Masukkan NIP (kosongkan jika tidak ada)..." value="{{ old('nip') }}" autofocus>
                            @error('nip') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-bold small">Nama Lengkap Guru</label>
                            <input type="text" name="nama_guru" class="form-control @error('nama_guru') is-invalid @enderror" placeholder="Contoh: Drs. Fiqri Haikal, M.Kom" value="{{ old('nama_guru') }}" required>
                            @error('nama_guru') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary py-2 rounded-pill fw-bold">Simpan Data Guru</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection