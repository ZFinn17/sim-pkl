<?php

namespace Database\Seeders;

use App\Models\GuruPembimbing;
use Illuminate\Database\Seeder;

class GuruPembimbingSeeder extends Seeder
{
    /** Data DEMO untuk development lokal. */
    public function run(): void
    {
        $guruDemo = [
            ['nip' => '198501012010011001', 'nama' => 'Drs. Bambang Sutrisno, M.Kom.', 'email' => 'bambang.sutrisno@icb.sch.id'],
            ['nip' => '199002152015022002', 'nama' => 'Rina Marlina, S.Kom.', 'email' => 'rina.marlina@icb.sch.id'],
            ['nip' => '198711202012011003', 'nama' => 'Agus Setiawan, S.T.', 'email' => 'agus.setiawan@icb.sch.id'],
        ];

        foreach ($guruDemo as $i => $guru) {
            GuruPembimbing::create([
                'nip' => $guru['nip'],
                'nama_lengkap' => $guru['nama'],
                'no_hp' => '0813' . str_pad((string) ($i + 1), 8, '0', STR_PAD_LEFT),
                'email' => $guru['email'],
                'status' => GuruPembimbing::STATUS_AKTIF,
            ]);
        }
    }
}
