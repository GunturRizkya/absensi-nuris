<?php

namespace Database\Factories;

use App\Models\Siswa;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Siswa>
 */
class SiswaFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nisn' => fake()->unique()->numerify('##########'),
            'nama_lengkap' => fake('id_ID')->name(),
            'jenis_kelamin' => fake()->randomElement(['L', 'P']),
            'no_hp_ortu' => fake()->numerify('08##########'),
            'qr_code' => fake()->unique()->bothify('QR########'),
            'card_number' => fake()->unique()->numerify('##########'),
            'fingerprint_id' => fake()->unique()->numerify('########'),
        ];
    }
}
