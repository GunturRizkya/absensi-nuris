<?php

namespace Database\Factories;

use App\Models\Jurusan;
use App\Models\Kelas;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Kelas>
 */
class KelasFactory extends Factory
{
    public function definition(): array
    {
        $tingkat = fake()->randomElement(['X', 'XI', 'XII']);

        return [
            'jurusan_id' => Jurusan::inRandomOrder()->value('id') ?? Jurusan::factory(),
            'wali_kelas_id' => null,
            'nama_kelas' => $tingkat.' '.strtoupper(fake()->lexify('???')).' '.fake()->numberBetween(1, 4),
            'tingkat' => $tingkat,
        ];
    }
}
