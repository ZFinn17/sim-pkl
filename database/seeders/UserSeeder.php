<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Data DEMO untuk development lokal.
     * Password default seluruh akun demo: "password".
     */
    public function run(): void
    {
        User::create([
            'nis_nip' => 'ADM-0001',
            'nama_lengkap' => 'Admin Hubin',
            'kelas' => null,
            'role' => User::ROLE_ADMIN,
            'no_hp' => '081200000001',
            'is_active' => true,
            'email' => 'admin@simpkl.test',
            'password' => Hash::make('password'),
        ]);

        $siswaDemo = [
            ['nis' => '2324.10.001', 'nama' => 'Ahmad Fauzan Ramadhan', 'kelas' => 'XI RPL 1'],
            ['nis' => '2324.10.002', 'nama' => 'Siti Nur Aisyah', 'kelas' => 'XI RPL 1'],
            ['nis' => '2324.10.003', 'nama' => 'Muhammad Rizki Hidayat', 'kelas' => 'XI RPL 2'],
            ['nis' => '2324.10.004', 'nama' => 'Dewi Lestari Putri', 'kelas' => 'XI RPL 2'],
            ['nis' => '2324.10.005', 'nama' => 'Reza Aditya Pratama', 'kelas' => 'XI RPL 1'],
        ];

        foreach ($siswaDemo as $i => $siswa) {
            User::create([
                'nis_nip' => $siswa['nis'],
                'nama_lengkap' => $siswa['nama'],
                'kelas' => $siswa['kelas'],
                'role' => User::ROLE_SISWA,
                'no_hp' => '0812' . str_pad((string) ($i + 1), 8, '0', STR_PAD_LEFT),
                'is_active' => true,
                'email' => 'siswa' . ($i + 1) . '@simpkl.test',
                'password' => Hash::make('password'),
            ]);
        }
    }
}
