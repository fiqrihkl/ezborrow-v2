@extends('layouts.app')

@section('title', 'Kenaikan Kelas Massal')

@section('content')
<div class="container-fluid py-4">
    <div class="mb-4">
        <h3 class="fw-bold text-dark">Kenaikan Kelas Massal</h3>
        <div class="alert alert-warning border-0 shadow-sm rounded-4 d-flex align-items-center">
            <i class="bi bi-exclamation-triangle-fill fs-4 me-3 text-warning"></i>
            <div>
                <strong class="text-dark">Instruksi Penting (Metode Top-Down):</strong> <br>
                Luluskan dulu kelas tertinggi (contoh: Kelas IX), baru naikkan kelas di bawahnya. Hal ini untuk mencegah tumpang tindih data siswa.
            </div>
        </div>
    </div>

    <form id="form-promotion" action="{{ route('promotion.process') }}" method="POST">
        @csrf
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-4 sticky-top" style="top: 20px; z-index: 1;">
                    <div class="card-body p-4">
                        <div class="mb-4 text-center">
                            <div class="bg-secondary bg-opacity-10 text-primary rounded-circle d-inline-flex align-items-center justify-content-center mb-2" style="width: 50px; height: 50px;">
                                <i class="bi bi-gear-fill fs-4"></i>
                            </div>
                            <h6 class="fw-bold d-block">Konfigurasi Kenaikan</h6>
                        </div>

                        <div class="mb-3">
                            <label class="fw-bold small mb-2 text-secondary">1. PILIH KELAS ASAL</label>
                            <select id="kelas_asal" name="kelas_asal" class="form-select rounded-3 py-2 shadow-xs" required>
                                <option value="">-- Pilih Kelas --</option>
                                @foreach($kelas as $k)
                                    <option value="{{ $k->id }}">{{ $k->nama_kelas }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="fw-bold small mb-2 text-secondary">2. PILIH AKSI</label>
                            <select name="aksi" id="aksi_select" class="form-select rounded-3 py-2 shadow-xs" required>
                                <option value="naik">Naikkan ke Kelas...</option>
                                <option value="lulus">Set sebagai LULUS / ALUMNI</option>
                            </select>
                        </div>

                        <div id="div_tujuan" class="mb-4">
                            <label class="fw-bold small mb-2 text-secondary">3. PILIH KELAS TUJUAN</label>
                            <select id="kelas_tujuan" name="kelas_tujuan" class="form-select rounded-3 py-2 shadow-xs">
                                <option value="">-- Pilih Kelas Tujuan --</option>
                                @foreach($kelas as $k)
                                    <option value="{{ $k->id }}">{{ $k->nama_kelas }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <button type="button" onclick="validateAndProcess()" class="btn btn-primary w-100 py-3 rounded-pill fw-bold shadow-sm">
                            <i class="bi bi-arrow-repeat me-2"></i>PROSES SEKARANG
                        </button>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                    <div class="card-header bg-white p-4 border-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="fw-bold mb-0 text-dark">Daftar Siswa di Kelas Terpilih</h6>
                                <span id="selected-count" class="badge bg-light text-primary border rounded-pill mt-1">0 Siswa terpilih</span>
                            </div>
                            <div class="form-check custom-checkbox">
                                <input class="form-check-input" type="checkbox" id="checkAll">
                                <label class="form-check-label small fw-bold text-secondary" for="checkAll">Pilih Semua</label>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive" style="max-height: 600px;">
                        <table class="table align-middle table-hover mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th width="60" class="ps-4">Pilih</th>
                                    <th>Identitas Siswa</th>
                                </tr>
                            </thead>
                            <tbody id="siswa_list">
                                <tr>
                                    <td colspan="2" class="text-center py-5 text-muted italic">
                                        <i class="bi bi-people display-4 opacity-10 d-block mb-2"></i>
                                        Pilih kelas asal terlebih dahulu.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@section('js')
<script>
    // 1. Logika Ambil Siswa via Fetch API
    document.getElementById('kelas_asal').addEventListener('change', function() {
        let kelasId = this.value;
        let list = document.getElementById('siswa_list');
        
        if(!kelasId) {
            list.innerHTML = '<tr><td colspan="2" class="text-center py-5 text-muted italic">Pilih kelas asal terlebih dahulu.</td></tr>';
            updateSelectedCount();
            return;
        }

        list.innerHTML = '<tr><td colspan="2" class="text-center py-5"><div class="spinner-border text-primary" role="status"></div><div class="mt-2 small text-muted">Memuat data...</div></td></tr>';

        fetch(`/admin/promotion/get-siswa/${kelasId}`)
            .then(res => res.json())
            .then(data => {
                list.innerHTML = '';
                if(data.length === 0) {
                    list.innerHTML = '<tr><td colspan="2" class="text-center py-5 text-danger fw-bold"><i class="bi bi-exclamation-octagon d-block mb-2 fs-2"></i>Tidak ada siswa aktif di kelas ini.</td></tr>';
                }
                data.forEach(s => {
                    list.innerHTML += `
                        <tr style="cursor: pointer" onclick="toggleRow(this)">
                            <td width="60" class="ps-4">
                                <input type="checkbox" name="siswa_ids[]" value="${s.id}" class="form-check-input checkItem" onclick="event.stopPropagation(); updateSelectedCount();">
                            </td>
                            <td class="py-3">
                                <div class="fw-bold text-dark text-uppercase">${s.nama_siswa}</div>
                                <div class="text-muted small">NIS: ${s.nis} | <span class="text-primary">${s.unique_id}</span></div>
                            </td>
                        </tr>
                    `;
                });
                updateSelectedCount();
            })
            .catch(err => {
                Toast.fire({ icon: 'error', title: 'Gagal mengambil data' });
            });
    });

    // 2. Logika Check All
    document.getElementById('checkAll').addEventListener('click', function() {
        document.querySelectorAll('.checkItem').forEach(item => item.checked = this.checked);
        updateSelectedCount();
    });

    function toggleRow(row) {
        const checkbox = row.querySelector('.checkItem');
        checkbox.checked = !checkbox.checked;
        updateSelectedCount();
    }

    function updateSelectedCount() {
        const count = document.querySelectorAll('.checkItem:checked').length;
        document.getElementById('selected-count').innerText = `${count} Siswa terpilih`;
    }

    // 3. Toggle kelas tujuan jika aksi = lulus
    document.getElementById('aksi_select').addEventListener('change', function() {
        document.getElementById('div_tujuan').style.display = (this.value === 'lulus') ? 'none' : 'block';
    });

    // 4. VALIDASI DAN KONFIRMASI MEWAH
    function validateAndProcess() {
        const kelasAsal = document.getElementById('kelas_asal').value;
        const aksi = document.getElementById('aksi_select').value;
        const kelasTujuan = document.getElementById('kelas_tujuan').value;
        const siswaChecked = document.querySelectorAll('.checkItem:checked').length;

        // Peringatan jika form tidak lengkap
        if (!kelasAsal) {
            Toast.fire({ icon: 'warning', title: 'Pilih kelas asal!' });
            return;
        }
        if (siswaChecked === 0) {
            Toast.fire({ icon: 'warning', title: 'Pilih minimal satu siswa!' });
            return;
        }
        if (aksi === 'naik' && !kelasTujuan) {
            Toast.fire({ icon: 'warning', title: 'Pilih kelas tujuan!' });
            return;
        }

        // Dialog Konfirmasi Premium
        const textKonfirmasi = (aksi === 'lulus') 
            ? `Siswa terpilih akan diset sebagai <b>LULUS / ALUMNI</b>.` 
            : `Siswa terpilih akan dipindahkan ke kelas tujuan.`;

        Swal.fire({
            title: '<span style="color: #1e293b; font-weight: 800;">KONFIRMASI KENAIKAN</span>',
            html: `${textKonfirmasi} <br><br> <div class="p-3 bg-light rounded-4 border"><b>${siswaChecked} Siswa</b> akan diproses.</div>`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#4361ee',
            cancelButtonColor: '#f1f5f9',
            confirmButtonText: 'YA, PROSES SEKARANG',
            cancelButtonText: '<span style="color: #64748b;">BATAL</span>',
            reverseButtons: true,
            background: '#ffffff',
            customClass: {
                popup: 'rounded-5 shadow-lg border-0',
                confirmButton: 'rounded-pill px-4 py-2 fw-bold',
                cancelButton: 'rounded-pill px-4 py-2 fw-bold'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                // Tampilkan loading saat proses berlangsung
                Swal.fire({
                    title: 'Sedang Memproses...',
                    html: 'Mohon tidak menutup halaman ini.',
                    allowOutsideClick: false,
                    didOpen: () => { Swal.showLoading(); }
                });
                document.getElementById('form-promotion').submit();
            }
        });
    }
</script>

<style>
    .shadow-xs { box-shadow: 0 1px 2px rgba(0,0,0,0.05); }
    .custom-checkbox .form-check-input:checked { background-color: #4361ee; border-color: #4361ee; }
    .table-hover tbody tr:hover { background-color: rgba(67, 97, 238, 0.05); }
    .checkItem { width: 20px; height: 20px; border-radius: 6px !important; }
</style>
@endsection