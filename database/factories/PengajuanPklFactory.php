<?php

namespace Database\Factories;

use App\Models\PengajuanPkl;
use App\Models\Perusahaan;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PengajuanPkl>
 */
class PengajuanPklFactory extends Factory
{
    protected $model = PengajuanPkl::class;

    public function definition(): array
    {
        return [
            'kode_pengajuan' => 'PKL-' . now()->year . '-' . fake()->unique()->numerify('####'),
            'jenis_pengajuan' => PengajuanPkl::JENIS_INDIVIDUAL,
            'ketua_siswa_id' => User::factory()->siswa(),
            'perusahaan_id' => Perusahaan::factory(),
            'guru_pembimbing_id' => null,
            'tgl_pengajuan' => now()->toDateString(),
            'tgl_mulai_pkl' => now()->addWeeks(2)->toDateString(),
            'tgl_selesai_pkl' => now()->addMonths(3)->toDateString(),
            'status' => PengajuanPkl::STATUS_SUBMITTED,
        ];
    }

    public function waitingCompany(): static
    {
        return $this->state(fn () => ['status' => PengajuanPkl::STATUS_WAITING_COMPANY]);
    }

    public function accepted(): static
    {
        return $this->state(fn () => ['status' => PengajuanPkl::STATUS_ACCEPTED, 'accepted_at' => now()]);
    }

    public function rejected(): static
    {
        return $this->state(fn () => [
            'status' => PengajuanPkl::STATUS_REJECTED,
            'rejected_at' => now(),
            'alasan_penolakan' => 'Kuota penuh.',
        ]);
    }
}
