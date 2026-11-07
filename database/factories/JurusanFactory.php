<?php

namespace Database\Factories;

use App\Models\Jurusan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Jurusan>
 */
class JurusanFactory extends Factory
{
    public function definition(): array
    {
        return [
            'kode_jurusan' => fake()->unique()->bothify('JRS-###'),
            'nama_jurusan' => 'Jurusan '.ucwords(fake('id_ID')->words(2, true)),
        ];
    }
}
