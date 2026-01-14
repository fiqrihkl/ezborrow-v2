@extends('layouts.app')

@section('title', 'Edit Mata Pelajaran')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-md-7">
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
                            <div class="col-md-4">
                                <label class="form-label fw-bold small">Kode Mapel</label>
                                <input type="text" name="kode_mapel" 
                                       class="form-control @error('kode_mapel') is-invalid @enderror" 
                                       value="{{ old('kode_mapel', $mapel->kode_mapel) }}" 
                                       placeholder="Contoh: INF-09" required>
                                @error('kode_mapel') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-8">
                                <label class="form-label fw-bold small">Nama Mata Pelajaran</label>
                                <input type="text" name="nama_mapel" 
                                       class="form-control @error('nama_mapel') is-invalid @enderror" 
                                       value="{{ old('nama_mapel', $mapel->nama_mapel) }}" 
                                       placeholder="Contoh: Informatika-9" required>
                                @error('nama_mapel') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-12">
                                <label class="form-label fw-bold small">Deskripsi (Opsional)</label>
                                <textarea name="deskripsi" class="form-control @error('deskripsi') is-invalid @enderror" 
                                          rows="2" placeholder="Penjelasan singkat mapel...">{{ old('deskripsi', $mapel->deskripsi) }}</textarea>
                                @error('deskripsi') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-12">
                                <label class="form-label fw-bold small">Guru Pengampu</label>
                                <select name="guru_ids[]" class="form-select @error('guru_ids') is-invalid @enderror" 
                                        multiple style="height: 180px;" required>
                                    @foreach($gurus as $g)
                                        <option value="{{ $g->id }}" 
                                            {{ in_array($g->id, old('guru_ids', $selectedGurus)) ? 'selected' : '' }}>
                                            {{ $g->nama_guru }} (NIP: {{ $g->nip }})
                                        </option>
                                    @endforeach
                                </select>
                                <div class="mt-2 p-2 bg-light rounded border small text-muted">
                                    <i class="bi bi-info-circle me-1"></i> Tahan <strong>Ctrl</strong> (Windows) atau <strong>Command</strong> (Mac) untuk memilih lebih dari satu guru.
                                </div>
                                @error('guru_ids') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <hr class="my-4 opacity-50">

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('mapel.index') }}" class="btn btn-light px-4 rounded-pill">Batal</a>
                            <button type="submit" class="btn btn-warning px-4 rounded-pill fw-bold shadow-sm">
                                <i class="bi bi-pencil-square me-2"></i> Perbarui Mapel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection