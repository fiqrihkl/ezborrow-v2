@extends('layouts.app')

@section('title', 'Manajemen Guru')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-0">Daftar Guru</h3>
            <p class="text-muted small">Kelola data guru pengajar</p>
        </div>
        <a href="{{ route('guru.create') }}" class="btn btn-primary rounded-pill px-4 shadow-sm">
            <i class="bi bi-person-plus me-1"></i> Tambah Guru
        </a>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-muted small">
                    <tr>
                        <th class="ps-4">NIP</th>
                        <th>NAMA GURU</th>
                        <th class="text-end pe-4">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($gurus as $guru)
                    <tr>
                        <td class="ps-4"><span class="badge bg-light text-dark border fw-normal">{{ $guru->nip }}</span></td>
                        <td class="fw-bold">{{ $guru->nama_guru }}</td>
                        <td class="text-end pe-4">
                            <div class="btn-group shadow-sm rounded-3">
                                <a href="{{ route('guru.edit', $guru->id) }}" class="btn btn-sm btn-white border">
                                    <i class="bi bi-pencil-square text-warning"></i>
                                </a>
                                <form action="{{ route('guru.destroy', $guru->id) }}" method="POST" onsubmit="return confirm('Hapus data guru ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-white border">
                                        <i class="bi bi-trash3 text-danger"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="3" class="text-center py-5 text-muted">Belum ada data guru.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection