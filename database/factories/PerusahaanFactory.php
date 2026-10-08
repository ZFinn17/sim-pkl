<?php

namespace Database\Factories;

use App\Models\Perusahaan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Perusahaan>
 */
class PerusahaanFactory extends Factory
{
    protected $model = Perusahaan::class;

    public function definition(): array
    {
        return [
            'user_id' => null,
            'nama_perusahaan' => fake()->company() . ' (Demo)',
            'alamat_lengkap' => fake()->address(),
            'nama_pic' => fake()->name(),
            'kontak_hrd' => fake()->numerify('022-7#######'),
            'email_hrd' => fake()->unique()->companyEmail(),
            'kuota_maksimal' => 5,
            'kuota_terisi' => 0,
            'status_mitra' => Perusahaan::STATUS_AKTIF,
            'deskripsi' => fake()->catchPhrase(),
        ];
    }

    public function nonaktif(): static
    {
        return $this->state(fn () => ['status_mitra' => Perusahaan::STATUS_NONAKTIF]);
    }

    public function kuotaPenuh(): static
    {
        return $this->state(fn (array $attributes) => ['kuota_terisi' => $attributes['kuota_maksimal'] ?? 5]);
    }
}
