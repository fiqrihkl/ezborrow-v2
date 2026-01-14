<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use App\Models\Kelas;
use Illuminate\Http\Request;

class PromotionController extends Controller
{
    public function index()
    {
        $kelas = Kelas::all();
        return view('admin.promotion.index', compact('kelas'));
    }

    public function process(Request $request)
    {
        $request->validate([
            'kelas_asal' => 'required',
            'aksi' => 'required', 
            'siswa_ids' => 'required|array'
        ]);

        if ($request->aksi == 'lulus') {
            // Untuk yang Lulus (Alumni)
            Siswa::whereIn('id', $request->siswa_ids)->update([
                'status' => 'alumni',
                'kelas_id' => null // Dilepas dari kelas agar kelas asal kosong
            ]);
            return back()->with('success', 'Siswa berhasil dipindahkan ke status Alumni.');
        } 
        
        if ($request->aksi == 'naik') {
            $request->validate(['kelas_tujuan' => 'required']);
            
            // Untuk yang Naik Kelas
            // Hanya update kelas_id, biarkan status tetap (aktif/nonaktif skorsing)
            Siswa::whereIn('id', $request->siswa_ids)->update([
                'kelas_id' => $request->kelas_tujuan
            ]);
            return back()->with('success', 'Proses kenaikan kelas berhasil.');
        }
    }

    // API untuk mengambil daftar siswa berdasarkan kelas (untuk AJAX)
    public function getSiswaByKelas($kelas_id)
    {
        // Hanya menampilkan siswa yang masih 'aktif' untuk dipromosikan
        return Siswa::where('kelas_id', $kelas_id)
                    ->where('status', 'aktif')
                    ->get();
    }
}