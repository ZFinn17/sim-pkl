<?php

namespace Database\Factories;

use App\Models\PengajuanPkl;
use App\Models\SuratPengantar;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<SuratPengantar>
 */
class SuratPengantarFactory extends Factory
{
    protected $model = SuratPengantar::class;

    public function definition(): array
    {
        return [
            'pengajuan_id' => PengajuanPkl::factory()->waitingCompany(),
            'nomor_surat' => '001/SIM-PKL/' . now()->format('m') . '/' . now()->year . '-' . fake()->unique()->numerify('###'),
            'tanggal_surat' => now()->toDateString(),
            'file_path' => 'surat-pengantar/test-' . Str::random(10) . '.pdf',
            'verification_token' => Str::random(48),
            'generated_at' => now(),
            'generated_by' => User::factory()->admin(),
        ];
    }
}
