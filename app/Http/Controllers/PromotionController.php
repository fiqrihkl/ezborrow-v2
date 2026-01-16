<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use App\Models\Kelas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PromotionController extends Controller
{
    public function index()
    {
        // Ambil semua kelas untuk dropdown
        $kelas = Kelas::orderBy('nama_kelas', 'asc')->get();
        return view('admin.promotion.index', compact('kelas'));
    }

    public function process(Request $request)
    {
        $request->validate([
            'kelas_asal' => 'required',
            'aksi' => 'required|in:naik,lulus', 
            'siswa_ids' => 'required|array'
        ], [
            'siswa_ids.required' => 'Pilih minimal satu siswa untuk diproses.'
        ]);

        try {
            DB::beginTransaction();

            if ($request->aksi == 'lulus') {
                /** * SOLUSI ERROR INTEGRITY CONSTRAINT:
                 * Kita tidak menset kelas_id ke NULL karena database Ibu melarangnya.
                 * Kita cukup ubah status menjadi 'alumni'. 
                 * Secara otomatis siswa ini tidak akan muncul di daftar kelas aktif.
                 */
                Siswa::whereIn('id', $request->siswa_ids)->update([
                    'status' => 'alumni',
                    'updated_at' => now()
                ]);

                DB::commit();
                return redirect()->route('siswa.index')->with('success', count($request->siswa_ids) . ' Siswa berhasil lulus dan menjadi Alumni.');
            } 
            
            if ($request->aksi == 'naik') {
                $request->validate(['kelas_tujuan' => 'required']);
                
                // Cek agar kelas asal dan tujuan tidak sama
                if ($request->kelas_asal == $request->kelas_tujuan) {
                    return back()->with('error', 'Kelas tujuan tidak boleh sama dengan kelas asal.');
                }

                Siswa::whereIn('id', $request->siswa_ids)->update([
                    'kelas_id' => $request->kelas_tujuan,
                    'status' => 'aktif', // Pastikan status tetap aktif setelah naik kelas
                    'updated_at' => now()
                ]);

                DB::commit();
                return redirect()->route('siswa.index')->with('success', 'Proses kenaikan kelas berhasil dilakukan.');
            }

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }

    /**
     * API untuk mengambil daftar siswa berdasarkan kelas (untuk AJAX di View)
     */
    public function getSiswaByKelas($kelas_id)
    {
        // Kita hanya mengambil siswa yang statusnya 'aktif' 
        // agar alumni atau siswa yang sudah keluar tidak ikut naik kelas lagi.
        return Siswa::where('kelas_id', $kelas_id)
                    ->where('status', 'aktif')
                    ->select('id', 'nama_siswa', 'nis', 'unique_id')
                    ->get();
    }
}   