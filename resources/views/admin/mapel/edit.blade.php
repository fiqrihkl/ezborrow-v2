@extends('layouts.app')

@section('title', 'Edit Mata Pelajaran')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-md-7">
            {{-- Header --}}
            <div class="d-flex align-items-center mb-4">
                <a href="{{ route('mapel.index') }}" class="btn btn-white shadow-sm rounded-circle me-3">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <div>
                    <h3 class="fw-bold mb-0">Edit Mapel</h3>
                    <p class="text-muted small mb-0">Memperbarui informasi: {{ $mapel->nama_mapel }}</p>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <form action="{{ route('mapel.update', $mapel->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row g-3">
                            {{-- Kode Mapel --}}
                            <div class="col-md-4">
                                <label class="form-label fw-bold small">Kode Mapel</label>
                                <input type="text" name="kode_mapel" 
                                       class="form-control @error('kode_mapel') is-invalid @enderror" 
                                       value="{{ old('kode_mapel', $mapel->kode_mapel) }}" 
                                       placeholder="Contoh: INF-09" required>
                                @error('kode_mapel') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            {{-- Nama Mapel --}}
                            <div class="col-md-8">
                                <label class="form-label fw-bold small">Nama Mata Pelajaran</label>
                                <input type="text" name="nama_mapel" 
                                       class="form-control @error('nama_mapel') is-invalid @enderror" 
                                       value="{{ old('nama_mapel', $mapel->nama_mapel) }}" 
                                       placeholder="Contoh: Informatika-9" required>
                                @error('nama_mapel') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            {{-- Deskripsi --}}
                            <div class="col-md-12">
                                <label class="form-label fw-bold small">Deskripsi (Opsional)</label>
                                <textarea name="deskripsi" class="form-control @error('deskripsi') is-invalid @enderror" 
                                          rows="2" placeholder="Penjelasan singkat mapel...">{{ old('deskripsi', $mapel->deskripsi) }}</textarea>
                                @error('deskripsi') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            {{-- Guru Pengampu (Sistem Ceklis) --}}
                            <div class="col-md-12">
                                <label class="form-label fw-bold small mb-2">Guru Pengampu</label>
                                <div class="border rounded-3 p-3 bg-light" style="max-height: 250px; overflow-y: auto;">
                                    <div class="row g-2">
                                        @foreach($gurus as $g)
                                        <div class="col-12 col-sm-6">
                                            <div class="form-check p-2 border rounded bg-white shadow-sm h-100">
                                                <input class="form-check-input ms-1" type="checkbox" name="guru_ids[]" 
                                                       value="{{ $g->id }}" id="guru_{{ $g->id }}"
                                                       {{ in_array($g->id, old('guru_ids', $selectedGurus)) ? 'checked' : '' }}>
                                                <label class="form-check-label ms-2 small" for="guru_{{ $g->id }}">
                                                    <strong>{{ $g->nama_guru }}</strong><br>
                                                    <span class="text-muted">NIP: {{ $g->nip }}</span>
                                                </label>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="mt-2 p-2 bg-light-info rounded border-start border-info border-4 small text-muted">
                                    <i class="bi bi-info-circle-fill text-info me-1"></i> 
                                    Cukup pilih/centang guru yang mengampu mapel ini.
                                </div>
                                @error('guru_ids') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <hr class="my-4 opacity-50">

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('mapel.index') }}" class="btn btn-light px-4 rounded-pill">Batal</a>
                            <button type="submit" class="btn btn-warning px-4 rounded-pill fw-bold shadow-sm text-dark">
                                <i class="bi bi-pencil-square me-2"></i> Perbarui Mapel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-light-info { background-color: #e7f3ff; }
    .form-check-input:checked { background-color: #ffc107; border-color: #ffc107; }
    .form-check:hover { background-color: #fff9e6 !important; border-color: #ffc107 !important; transition: 0.3s; }
</style>
@endsection