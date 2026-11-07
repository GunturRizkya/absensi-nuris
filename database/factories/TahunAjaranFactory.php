<?php

namespace Database\Factories;

use App\Models\TahunAjaran;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TahunAjaran>
 */
class TahunAjaranFactory extends Factory
{
    public function definition(): array
    {
        $awal = fake()->numberBetween(1990, 2080);

        return [
            'tahun' => $awal.'/'.($awal + 1),
            'semester' => fake()->randomElement(['1', '2']),
            'is_aktif' => false,
        ];
    }
}
