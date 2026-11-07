<?php

namespace Database\Factories;

use App\Models\MataPelajaran;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MataPelajaran>
 */
class MataPelajaranFactory extends Factory
{
    public function definition(): array
    {
        return [
            'jurusan_id' => null,
            'kode_mapel' => strtoupper(fake()->unique()->lexify('????')),
            'nama_mapel' => ucwords(fake('id_ID')->words(2, true)),
        ];
    }
}
