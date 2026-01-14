<?php

namespace App\Providers;

use Illuminate\Support\Facades\Schema; // Tambahkan ini di paling atas
use App\Models\Setting;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot()
    {
        // Cek dulu apakah tabel 'settings' sudah ada di database atau belum
        if (Schema::hasTable('settings')) {
            view()->share('sys_settings', Setting::pluck('value', 'key')->all());
        } else {
            // Jika belum ada (saat migrasi), kirim array kosong agar tidak error
            view()->share('sys_settings', []);
        }
    }
}
