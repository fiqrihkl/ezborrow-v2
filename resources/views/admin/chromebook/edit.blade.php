@extends('layouts.app')

@section('title', 'Edit Data Chromebook')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="d-flex align-items-center mb-4">
                <a href="{{ route('chromebook.index') }}" class="btn btn-white shadow-sm rounded-circle me-3">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <div>
                    <h3 class="fw-bold mb-0">Edit Chromebook</h3>
                    <p class="text-muted small mb-0">Update informasi unit: {{ $chromebook->no_unit }}</p>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <form action="{{ route('chromebook.update', $chromebook->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">Nomor Unit</label>
                                <input type="text" name="no_unit" class="form-control @error('no_unit') is-invalid @enderror" 
                                       value="{{ old('no_unit', $chromebook->no_unit) }}" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold small">Merek / Tipe</label>
                                <input type="text" name="merek" class="form-control @error('merek') is-invalid @enderror" 
                                       value="{{ old('merek', $chromebook->merek) }}" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold small">QR Code Unit</label>
                                <input type="text" name="qr_code_unit" class="form-control @error('qr_code_unit') is-invalid @enderror" 
                                       value="{{ old('qr_code_unit', $chromebook->qr_code_unit) }}" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold small">Posisi Loker</label>
                                <input type="text" name="loker" class="form-control @error('loker') is-invalid @enderror" 
                                       value="{{ old('loker', $chromebook->loker) }}" required>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label fw-bold small d-block">Status Perangkat</label>
                                <div class="btn-group w-100" role="group">
                                    <input type="radio" class="btn-check" name="status" id="st_tersedia" value="tersedia" {{ $chromebook->status == 'tersedia' ? 'checked' : '' }}>
                                    <label class="btn btn-outline-success" for="st_tersedia">Tersedia</label>

                                    <input type="radio" class="btn-check" name="status" id="st_dipinjam" value="dipinjam" {{ $chromebook->status == 'dipinjam' ? 'checked' : '' }}>
                                    <label class="btn btn-outline-warning" for="st_dipinjam">Dipinjam</label>

                                    <input type="radio" class="btn-check" name="status" id="st_rusak" value="rusak" {{ $chromebook->status == 'rusak' ? 'checked' : '' }}>
                                    <label class="btn btn-outline-danger" for="st_rusak">Rusak</label>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4 opacity-50">

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('chromebook.index') }}" class="btn btn-light px-4 rounded-pill">Batal</a>
                            <button type="submit" class="btn btn-primary px-4 rounded-pill fw-bold shadow-sm">
                                <i class="bi bi-arrow-repeat me-2"></i> Update Unit
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection