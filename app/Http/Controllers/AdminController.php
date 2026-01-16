<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use App\Models\Chromebook;
use App\Models\Peminjaman;
use App\Models\Voucher;
use App\Models\Kelas;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function index()
    {
        // 1. Statistik Dasar
        $stats = [
            'total_siswa'    => Siswa::where('status', 'aktif')->count(),
            'unit_tersedia'  => Chromebook::where('status', 'tersedia')->count(),
            'unit_dipinjam'  => Chromebook::where('status', 'dipinjam')->count(),
            'stok_voucher'   => Voucher::count(),
        ];

        // TAMBAHKAN INI: Ambil daftar kelas yang stok vouchernya kritis (misal <= 5)
        $stokKritisPerKelas = Kelas::withCount('vouchers')
        ->having('vouchers_count', '<=', 5) // Ambang batas kritis per kelas
        ->orderBy('vouchers_count', 'asc')
        ->get();

        // 2. Logika Grafik (7 Hari Terakhir)
        $days = [];
        $counts = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $days[] = $date->translatedFormat('D');
            $counts[] = Peminjaman::whereDate('waktu_pinjam', $date->format('Y-m-d'))->count();
        }

        // 3. Peminjam Aktif
        $peminjamAktif = Peminjaman::with(['siswa', 'chromebook'])
            ->whereNull('waktu_kembali')
            ->latest('waktu_pinjam')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'stats', 
            'days', 
            'counts', 
            'peminjamAktif', 
            'stokKritisPerKelas' // <--- PASTIKAN VARIABEL INI ADA DI SINI
        ));
    }
}