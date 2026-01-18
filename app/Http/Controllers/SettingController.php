<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::pluck('value', 'key')->all();
        return view('admin.setting.index', compact('settings'));
    }

    public function update(Request $request)
    {
        // 1. Validasi Input
        // Jika ingin nama sekolah BISA dihapus/dikosongkan, ganti 'required' menjadi 'nullable'
        $request->validate([
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'school_name' => 'nullable|string|max:255', // Diubah ke nullable
            'school_email' => 'nullable|email',
            'app_name' => 'nullable|string|max:50',
        ]);

        // 2. Update data teks
        $data = $request->except(['_token', 'logo', 'remove_logo']);
        
        foreach ($data as $key => $value) {
            // Kita gunakan null coalescing untuk memastikan jika input kosong, value di DB jadi string kosong atau null
            Setting::updateOrCreate(
                ['key' => $key], 
                ['value' => $value !== null ? trim($value) : '']
            );
        }

        // 3. Logika Hapus Logo
        if ($request->remove_logo == '1') {
            $this->deleteOldLogo();
            Setting::updateOrCreate(['key' => 'school_logo'], ['value' => '']);
        }

        // 4. Logika Upload Logo Baru
        if ($request->hasFile('logo')) {
            $this->deleteOldLogo();
            $path = $request->file('logo')->store('logos', 'public');
            $url = Storage::url($path);
            
            Setting::updateOrCreate(
                ['key' => 'school_logo'], 
                ['value' => $url]
            );
        }

        return redirect()->back()->with('success', 'Pengaturan sistem berhasil diperbarui.');
    }

    private function deleteOldLogo()
    {
        $oldLogoSetting = Setting::where('key', 'school_logo')->first();
        if ($oldLogoSetting && !empty($oldLogoSetting->value)) {
            $oldPath = str_replace('/storage/', '', $oldLogoSetting->value);
            if (Storage::disk('public')->exists($oldPath)) {
                Storage::disk('public')->delete($oldPath);
            }
        }
    }
}