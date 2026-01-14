@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-4">Tambah Mapel</h5>
                    <form action="{{ route('mapel.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Nama Mata Pelajaran</label>
                            <input type="text" name="nama_mapel" class="form-control" placeholder="Contoh: Pemrograman Web" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Kode Mapel</label>
                            <input type="text" name="kode_mapel" class="form-control" placeholder="Contoh: INF-07" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold">Deskripsi (Opsional)</label>
                            <textarea name="deskripsi" class="form-control" rows="2"></textarea>
                        </div>
                        <div class="mb-4">
                            <label class="form-label small fw-bold">Pilih Guru (Bisa pilih lebih dari satu)</label>
                            <select name="guru_ids[]" class="form-select" multiple style="height: 150px;" required>
                                @foreach($gurus as $g)
                                    <option value="{{ $g->id }}">{{ $g->nama_guru }}</option>
                                @endforeach
                            </select>
                            <small class="text-muted">Tahan tombol Ctrl (Windows) / Command (Mac) untuk pilih banyak.</small>
                        </div>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary rounded-pill">Simpan Mapel</button>
                            <a href="{{ route('mapel.index') }}" class="btn btn-light rounded-pill">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection