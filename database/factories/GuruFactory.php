<?php

namespace Database\Factories;

use App\Models\Guru;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Guru>
 */
class GuruFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => null,
            'nip' => fake()->unique()->numerify('19##########'),
            'nama_lengkap' => fake('id_ID')->name(),
            'no_hp' => fake()->numerify('08##########'),
        ];
    }
}
