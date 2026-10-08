<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    /**
     * Override config/simpkl.php dengan nilai dari tabel settings, jika ada.
     * Dibungkus try/catch karena tabel belum tentu ada saat artisan command
     * tertentu (mis. migrate pertama kali, key:generate) dijalankan.
     */
    public function boot(): void
    {
        try {
            if (! Schema::hasTable('settings')) {
                return;
            }

            $overrides = Setting::query()->pluck('value', 'key');

            if ($overrides->isEmpty()) {
                return;
            }

            $map = [
                'sekolah.nama' => 'simpkl.sekolah.nama',
                'sekolah.alamat' => 'simpkl.sekolah.alamat',
                'sekolah.telepon' => 'simpkl.sekolah.telepon',
                'sekolah.email' => 'simpkl.sekolah.email',
                'sekolah.website' => 'simpkl.sekolah.website',
                'sekolah.kepala_sekolah' => 'simpkl.sekolah.kepala_sekolah',
                'sekolah.nip_kepala_sekolah' => 'simpkl.sekolah.nip_kepala_sekolah',
                'surat.kode' => 'simpkl.surat.kode',
                'surat.format' => 'simpkl.surat.format',
            ];

            foreach ($map as $settingKey => $configKey) {
                if ($overrides->has($settingKey) && $overrides->get($settingKey) !== null) {
                    config([$configKey => $overrides->get($settingKey)]);
                }
            }
        } catch (\Throwable) {
            // Database belum siap (mis. saat `composer install` menjalankan artisan
            // sebelum migrate) - abaikan saja, config default tetap dipakai.
        }
    }
}
