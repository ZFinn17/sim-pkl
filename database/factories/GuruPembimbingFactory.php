<?php

namespace Database\Factories;

use App\Models\GuruPembimbing;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<GuruPembimbing>
 */
class GuruPembimbingFactory extends Factory
{
    protected $model = GuruPembimbing::class;

    public function definition(): array
    {
        return [
            'nip' => fake()->unique()->numerify('19#############'),
            'nama_lengkap' => fake()->name(),
            'no_hp' => fake()->numerify('08##########'),
            'email' => fake()->unique()->safeEmail(),
            'status' => GuruPembimbing::STATUS_AKTIF,
        ];
    }

    public function nonaktif(): static
    {
        return $this->state(fn () => ['status' => GuruPembimbing::STATUS_NONAKTIF]);
    }
}
