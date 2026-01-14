@extends('layouts.app')

@section('title', 'Manajemen Mata Pelajaran')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-0">Mata Pelajaran</h3>
            <p class="text-muted small">Kelola kurikulum dan penugasan guru pengampu</p>
        </div>
        <a href="{{ route('mapel.create') }}" class="btn btn-primary rounded-pill px-4 shadow-sm">
            <i class="bi bi-plus-lg me-1"></i> Tambah Mapel
        </a>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-muted small">
                    <tr>
                        <th class="ps-4">KODE</th>
                        <th>NAMA MATA PELAJARAN</th>
                        <th>DESKRIPSI</th>
                        <th>GURU PENGAMPU</th>
                        <th class="text-end pe-4">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($mapels as $m)
                    <tr>
                        <td class="ps-4">
                            <span class="badge bg-light text-primary border fw-bold">{{ $m->kode_mapel }}</span>
                        </td>
                        <td>
                            <div class="fw-bold">{{ $m->nama_mapel }}</div>
                        </td>
                        <td>
                            <small class="text-muted">{{ Str::limit($m->deskripsi, 40) ?? '-' }}</small>
                        </td>
                        <td>
                            <div class="d-flex flex-wrap gap-1">
                                @forelse($m->gurus as $g)
                                    <span class="badge bg-info-subtle text-info border border-info-subtle rounded-pill px-2 fw-normal" style="font-size: 0.75rem;">
                                        <i class="bi bi-person me-1"></i>{{ $g->nama_guru }}
                                    </span>
                                @empty
                                    <span class="text-danger small">Belum ada guru</span>
                                @endforelse
                            </div>
                        </td>
                        <td class="text-end pe-4">
                            <div class="btn-group shadow-sm rounded-3">
                                <a href="{{ route('mapel.edit', $m->id) }}" class="btn btn-sm btn-white border" title="Edit Mapel">
                                    <i class="bi bi-pencil-square text-warning"></i>
                                </a>
                                <form action="{{ route('mapel.destroy', $m->id) }}" method="POST" onsubmit="return confirm('Hapus mapel {{ $m->nama_mapel }}?')">
                                    @csrf 
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-white border" title="Hapus Mapel">
                                        <i class="bi bi-trash3 text-danger"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">
                            <i class="bi bi-book-half display-1 opacity-10 d-block mb-3"></i>
                            Belum ada data mata pelajaran.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($mapels->hasPages())
        <div class="card-footer bg-white border-0 py-3">
            {{ $mapels->links() }}
        </div>
        @endif
    </div>
</div>
@endsection