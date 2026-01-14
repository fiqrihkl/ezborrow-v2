@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold">Manajemen Kelas</h3>
        <a href="{{ route('kelas.create') }}" class="btn btn-primary rounded-pill px-4">Tambah Kelas</a>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-muted small">
                    <tr>
                        <th class="ps-4">NAMA KELAS</th>
                        <th>WALI KELAS</th>
                        <th class="text-center">JUMLAH SISWA</th>
                        <th class="text-end pe-4">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($kelas as $k)
                    <tr>
                        <td class="ps-4 fw-bold">{{ $k->nama_kelas }}</td>
                        <td>{{ $k->wali->nama_guru ?? 'Belum ada wali' }}</td>
                        <td class="text-center">
                            <span class="badge bg-primary-subtle text-primary rounded-pill">{{ $k->siswas_count }} Siswa</span>
                        </td>
                        <td class="text-end pe-4">
                            <div class="btn-group shadow-sm">
                                <a href="{{ route('kelas.edit', $k->id) }}" class="btn btn-sm btn-white border text-warning"><i class="bi bi-pencil-square"></i></a>
                                <form action="{{ route('kelas.destroy', $k->id) }}" method="POST" id="delete-form-{{ $k->id }}">
    @csrf @method('DELETE')
    <button type="button" class="btn btn-sm btn-white border" 
            onclick="confirmDelete('delete-form-{{ $k->id }}', '{{ $k->nama_kelas }}')">
        <i class="bi bi-trash text-danger"></i>
    </button>
</form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection