<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seeder utama untuk data DEMO/development SIM-PKL.
     * Jangan dijalankan di environment production.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            GuruPembimbingSeeder::class,
            PerusahaanSeeder::class,
            PengajuanPklSeeder::class,
        ]);
    }
}
