<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    /**
     * Menampilkan Halaman Pengaturan
     */
    public function index()
    {
        // Mengambil semua setting dan mengubahnya menjadi array key-value
        $settings = Setting::pluck('value', 'key')->all();
        
        return view('admin.setting.index', compact('settings'));
    }

    /**
     * Memperbarui Pengaturan Sistem
     */
    public function update(Request $request)
    {
        // 1. Validasi Input
        $request->validate([
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'school_name' => 'required|string|max:255',
            'school_email' => 'nullable|email',
        ]);

        // 2. Update data teks (Nama Sekolah, Alamat, dll)
        $data = $request->except(['_token', 'logo']);
        foreach ($data as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key], 
                ['value' => trim($value)]
            );
        }

        // 3. Logika Upload Logo
        if ($request->hasFile('logo')) {
            // Ambil data logo lama dari database
            $oldLogoSetting = Setting::where('key', 'school_logo')->first();

            if ($oldLogoSetting && $oldLogoSetting->value) {
                // Hapus file lama dari storage jika ada
                // Kita hapus prefix '/storage/' agar mendapatkan path aslinya
                $oldPath = str_replace('/storage/', '', $oldLogoSetting->value);
                Storage::disk('public')->delete($oldPath);
            }

            // Simpan file baru ke: storage/app/public/logos
            $path = $request->file('logo')->store('logos', 'public');
            
            // Generate URL publik: /storage/logos/namafile.png
            $url = Storage::url($path);
            
            // Simpan URL ke database
            Setting::updateOrCreate(
                ['key' => 'school_logo'], 
                ['value' => $url]
            );
        }

        return redirect()->back()->with('success', 'Selamat! Pengaturan sistem berhasil diperbarui.');
    }
}