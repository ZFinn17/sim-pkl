<?php

namespace Database\Seeders;

use App\Models\Perusahaan;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class PerusahaanSeeder extends Seeder
{
    /**
     * Data DEMO untuk development lokal.
     * PENTING: nama-nama perusahaan di bawah ini adalah data fiktif untuk keperluan
     * pengembangan/testing, BUKAN daftar mitra resmi SMK ICB Cinta Niaga.
     */
    public function run(): void
    {
        $perusahaanDemo = [
            [
                'nama' => 'PT Teknologi Nusantara (Demo)',
                'alamat' => 'Jl. Soekarno Hatta No. 123, Bandung',
                'pic' => 'Yusuf Maulana',
                'kontak' => '022-7300001',
                'email' => 'hrd@teknologinusantara.demo',
                'kuota' => 5,
                'deskripsi' => 'Perusahaan pengembangan software berbasis web dan mobile.',
            ],
            [
                'nama' => 'CV Digital Kreatif Bandung (Demo)',
                'alamat' => 'Jl. Dipatiukur No. 45, Bandung',
                'pic' => 'Anita Wulandari',
                'kontak' => '022-7300002',
                'email' => 'hrd@digitalkreatif.demo',
                'kuota' => 3,
                'deskripsi' => 'Agensi digital marketing dan desain UI/UX.',
            ],
            [
                'nama' => 'PT Mitra Industri Indonesia (Demo)',
                'alamat' => 'Jl. Soekarno Hatta No. 456, Bandung',
                'pic' => 'Hendra Gunawan',
                'kontak' => '022-7300003',
                'email' => 'hrd@mitraindustri.demo',
                'kuota' => 4,
                'deskripsi' => 'Perusahaan manufaktur dengan divisi IT internal.',
            ],
        ];

        foreach ($perusahaanDemo as $i => $data) {
            $user = User::create([
                'nis_nip' => null,
                'nama_lengkap' => $data['pic'],
                'kelas' => null,
                'role' => User::ROLE_PERUSAHAAN,
                'no_hp' => $data['kontak'],
                'is_active' => true,
                'email' => 'perusahaan' . ($i + 1) . '@simpkl.test',
                'password' => Hash::make('password'),
            ]);

            Perusahaan::create([
                'user_id' => $user->id,
                'nama_perusahaan' => $data['nama'],
                'alamat_lengkap' => $data['alamat'],
                'nama_pic' => $data['pic'],
                'kontak_hrd' => $data['kontak'],
                'email_hrd' => $data['email'],
                'kuota_maksimal' => $data['kuota'],
                'kuota_terisi' => 0,
                'status_mitra' => Perusahaan::STATUS_AKTIF,
                'deskripsi' => $data['deskripsi'],
            ]);
        }
    }
}
