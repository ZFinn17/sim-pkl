<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'nis_nip' => fake()->unique()->numerify('23##.##.###'),
            'nama_lengkap' => fake()->name(),
            'kelas' => fake()->randomElement(['XI RPL 1', 'XI RPL 2']),
            'role' => User::ROLE_SISWA,
            'no_hp' => fake()->numerify('08##########'),
            'is_active' => true,
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'remember_token' => Str::random(10),
        ];
    }

    public function admin(): static
    {
        return $this->state(fn () => [
            'role' => User::ROLE_ADMIN,
            'nis_nip' => null,
            'kelas' => null,
        ]);
    }

    public function siswa(): static
    {
        return $this->state(fn () => ['role' => User::ROLE_SISWA]);
    }

    public function perusahaanAccount(): static
    {
        return $this->state(fn () => [
            'role' => User::ROLE_PERUSAHAAN,
            'nis_nip' => null,
            'kelas' => null,
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn () => ['is_active' => false]);
    }

    public function unverified(): static
    {
        return $this->state(fn () => ['email_verified_at' => null]);
    }
}
