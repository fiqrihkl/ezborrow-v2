@extends('layouts.app')

@section('title', 'Tambah Mata Pelajaran')

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
                    <h3 class="fw-bold mb-0">Tambah Mapel</h3>
                    <p class="text-muted small mb-0">Buat mata pelajaran baru dan tentukan pengampunya</p>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <form action="{{ route('mapel.store') }}" method="POST">
                        @csrf
                        
                        <div class="row g-3">
                            {{-- Kode Mapel --}}
                            <div class="col-md-4">
                                <label class="form-label fw-bold small">Kode Mapel</label>
                                <input type="text" name="kode_mapel" 
                                       class="form-control @error('kode_mapel') is-invalid @enderror" 
                                       value="{{ old('kode_mapel') }}" 
                                       placeholder="Contoh: INF-09" required>
                                @error('kode_mapel') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            {{-- Nama Mapel --}}
                            <div class="col-md-8">
                                <label class="form-label fw-bold small">Nama Mata Pelajaran</label>
                                <input type="text" name="nama_mapel" 
                                       class="form-control @error('nama_mapel') is-invalid @enderror" 
                                       value="{{ old('nama_mapel') }}" 
                                       placeholder="Contoh: Pemrograman Web" required>
                                @error('nama_mapel') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            {{-- Deskripsi --}}
                            <div class="col-md-12">
                                <label class="form-label fw-bold small">Deskripsi (Opsional)</label>
                                <textarea name="deskripsi" class="form-control @error('deskripsi') is-invalid @enderror" 
                                          rows="2" placeholder="Penjelasan singkat mengenai mata pelajaran ini...">{{ old('deskripsi') }}</textarea>
                                @error('deskripsi') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            {{-- Guru Pengampu (Sistem Ceklis Mobile Friendly) --}}
                            <div class="col-md-12">
                                <label class="form-label fw-bold small mb-2">Pilih Guru Pengampu</label>
                                <div class="border rounded-3 p-3 bg-light" style="max-height: 250px; overflow-y: auto;">
                                    <div class="row g-2">
                                        @forelse($gurus as $g)
                                        <div class="col-12 col-sm-6">
                                            <div class="form-check p-2 border rounded bg-white shadow-sm h-100 custom-check-card">
                                                <input class="form-check-input ms-1" type="checkbox" name="guru_ids[]" 
                                                       value="{{ $g->id }}" id="guru_{{ $g->id }}"
                                                       {{ is_array(old('guru_ids')) && in_array($g->id, old('guru_ids')) ? 'checked' : '' }}>
                                                <label class="form-check-label ms-2 small w-100" for="guru_{{ $g->id }}">
                                                    <strong>{{ $g->nama_guru }}</strong><br>
                                                    <span class="text-muted" style="font-size: 0.75rem;">NIP: {{ $g->nip ?? '-' }}</span>
                                                </label>
                                            </div>
                                        </div>
                                        @empty
                                        <div class="col-12 text-center py-3 text-muted">
                                            <small>Data guru belum tersedia.</small>
                                        </div>
                                        @endforelse
                                    </div>
                                </div>
                                <div class="mt-2 p-2 bg-light-primary rounded border-start border-primary border-4 small text-muted">
                                    <i class="bi bi-info-circle-fill text-primary me-1"></i> 
                                    Bisa mencentang lebih dari satu guru pengampu.
                                </div>
                                @error('guru_ids') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <hr class="my-4 opacity-50">

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('mapel.index') }}" class="btn btn-light px-4 rounded-pill text-muted">Batal</a>
                            <button type="submit" class="btn btn-primary px-4 rounded-pill fw-bold shadow-sm">
                                <i class="bi bi-plus-circle me-2"></i> Simpan Mapel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-light-primary { background-color: #f0f4ff; }
    .custom-check-card:hover { 
        background-color: #f8faff !important; 
        border-color: #4361ee !important; 
        transition: 0.2s;
        cursor: pointer;
    }
    .form-check-input:checked { 
        background-color: #4361ee; 
        border-color: #4361ee; 
    }
    /* Memastikan area klik label memenuhi card */
    .form-check-label { cursor: pointer; }
</style>
@endsection