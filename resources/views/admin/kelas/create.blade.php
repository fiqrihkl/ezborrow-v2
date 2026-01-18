@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-4">Tambah Kelas Baru</h5>
                    <form action="{{ route('kelas.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Nama Kelas</label>
                            <input type="text" name="nama_kelas" class="form-control" placeholder="Contoh: VII A" required>
                        </div>
                        <div class="mb-4">
                            <label class="form-label small fw-bold">Pilih Wali Kelas</label>
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
                        </div>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary rounded-pill">Simpan Kelas</button>
                            <a href="{{ route('kelas.index') }}" class="btn btn-light rounded-pill">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection