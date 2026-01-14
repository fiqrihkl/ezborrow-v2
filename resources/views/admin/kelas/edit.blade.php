@extends('layouts.app')

@section('title', 'Edit Data Kelas')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="d-flex align-items-center mb-4">
                <a href="{{ route('kelas.index') }}" class="btn btn-white shadow-sm rounded-circle me-3">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <div>
                    <h3 class="fw-bold mb-0">Edit Kelas</h3>
                    <p class="text-muted small mb-0">Mengubah informasi kelas: {{ $kelas->nama_kelas }}</p>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <form action="{{ route('kelas.update', $kelas->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold small">Nama Kelas</label>
                            <input type="text" name="nama_kelas" 
                                   class="form-control @error('nama_kelas') is-invalid @enderror" 
                                   value="{{ old('nama_kelas', $kelas->nama_kelas) }}" 
                                   placeholder="Contoh: VII-A" required>
                            @error('nama_kelas') 
                                <div class="invalid-feedback">{{ $message }}</div> 
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold small">Wali Kelas</label>
                            <select name="guru_id" class="form-select @error('guru_id') is-invalid @enderror">
                                <option value="">-- Pilih Guru --</option>
                                @foreach($gurus as $g)
                                    @php
                                        // Cek apakah guru sudah punya kelas
                                        $sudahJadiWali = $g->kelas && (!isset($kelas) || $g->kelas->id !== $kelas->id);
                                    @endphp
                                    
                                    <option value="{{ $g->id }}" 
                                        {{ (old('guru_id', $kelas->guru_id ?? '') == $g->id) ? 'selected' : '' }}
                                        {{ $sudahJadiWali ? 'disabled' : '' }}>
                                        
                                        {{ $g->nama_guru }} 
                                        
                                        @if($sudahJadiWali)
                                            (Wali Kelas {{ $g->kelas->nama_kelas }})
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            @error('guru_id') 
                                <div class="invalid-feedback">{{ $message }}</div> 
                            @enderror
                            @error('guru_id') 
                                <div class="invalid-feedback">{{ $message }}</div> 
                            @enderror
                            <small class="text-muted mt-1 d-block">Guru yang dipilih akan bertanggung jawab atas laporan peminjaman kelas ini.</small>
                        </div>

                        <hr class="my-4 opacity-50">

                        <div class="p-3 bg-light rounded-3 mb-4 d-flex justify-content-between align-items-center">
                            <div class="small fw-bold text-muted text-uppercase">Total Siswa Terdaftar</div>
                            <div class="h5 mb-0 fw-bold text-primary">{{ $kelas->siswas()->count() }} Siswa</div>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('kelas.index') }}" class="btn btn-light px-4 rounded-pill">Batal</a>
                            <button type="submit" class="btn btn-warning px-4 rounded-pill fw-bold shadow-sm">
                                <i class="bi bi-pencil-square me-2"></i> Update Kelas
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="mt-4 p-3 bg-warning-subtle rounded-4 border border-warning border-dashed">
                <div class="d-flex">
                    <i class="bi bi-exclamation-triangle text-warning me-3 fs-4"></i>
                    <small class="text-dark">
                        <strong>Perhatian:</strong> Mengubah nama kelas akan berdampak pada identitas kelas yang muncul di kartu QR siswa dan laporan harian. Pastikan data sudah benar.
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection