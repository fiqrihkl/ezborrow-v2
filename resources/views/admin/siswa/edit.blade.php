@extends('layouts.app')

@section('title', 'Edit Data Siswa')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="d-flex align-items-center mb-4">
                <a href="{{ route('siswa.index') }}" class="btn btn-white shadow-sm rounded-circle me-3">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <div>
                    <h3 class="fw-bold mb-0">Edit Siswa</h3>
                    <p class="text-muted small mb-0">Memperbarui data: {{ $siswa->nama_siswa }}</p>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <form action="{{ route('siswa.update', $siswa->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label fw-bold small">Nama Lengkap Siswa</label>
                                <input type="text" name="nama_siswa" class="form-control @error('nama_siswa') is-invalid @enderror" 
                                       value="{{ old('nama_siswa', $siswa->nama_siswa) }}" required>
                                @error('nama_siswa') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            {{-- Contoh jika NIS ingin tetap dikirim tapi tidak diubah oleh Admin --}}
                            <input type="hidden" name="nis" value="{{ $siswa->nis }}">

                            <div class="col-md-6">
                                <label class="form-label fw-bold small">Unique ID / Nomor Kartu</label>
                                <input type="text" name="unique_id" class="form-control @error('unique_id') is-invalid @enderror" 
                                       value="{{ old('unique_id', $siswa->unique_id) }}" required>
                                @error('unique_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold small">Kelas</label>
                                <select name="kelas_id" class="form-select @error('kelas_id') is-invalid @enderror" required>
                                    @foreach($kelas as $k)
                                        <option value="{{ $k->id }}" {{ $siswa->kelas_id == $k->id ? 'selected' : '' }}>
                                            {{ $k->nama_kelas }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label fw-bold small d-block">Status Keaktifan</label>
                                <div class="btn-group w-100" role="group">
                                    <input type="radio" class="btn-check" name="status" id="status1" value="aktif" {{ $siswa->status == 'aktif' ? 'checked' : '' }}>
                                    <label class="btn btn-outline-success" for="status1">Aktif</label>

                                    <input type="radio" class="btn-check" name="status" id="status2" value="nonaktif" {{ $siswa->status == 'nonaktif' ? 'checked' : '' }}>
                                    <label class="btn btn-outline-danger" for="status2">Non-Aktif (Skors)</label>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4 opacity-50">

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('siswa.index') }}" class="btn btn-light px-4 rounded-pill">Batal</a>
                            <button type="submit" class="btn btn-warning px-4 rounded-pill fw-bold">
                                <i class="bi bi-pencil-square me-2"></i> Update Data
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection