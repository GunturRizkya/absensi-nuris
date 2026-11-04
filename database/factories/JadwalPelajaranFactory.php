<?php

namespace Database\Factories;

use App\Models\Guru;
use App\Models\JadwalPelajaran;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\TahunAjaran;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<JadwalPelajaran>
 */
class JadwalPelajaranFactory extends Factory
{
    public function definition(): array
    {
        $mulai = fake()->numberBetween(7, 14);
        $selesai = $mulai + fake()->randomElement([1, 2]);

        return [
            'tahun_ajaran_id' => TahunAjaran::where('is_aktif', true)->value('id') ?? TahunAjaran::factory(),
            'kelas_id' => Kelas::inRandomOrder()->value('id') ?? Kelas::factory(),
            'mata_pelajaran_id' => MataPelajaran::inRandomOrder()->value('id') ?? MataPelajaran::factory(),
            'guru_id' => Guru::inRandomOrder()->value('id') ?? Guru::factory(),
            'hari' => fake()->randomElement(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu']),
            'jam_mulai' => sprintf('%02d:00:00', $mulai),
            'jam_selesai' => sprintf('%02d:30:00', $selesai),
        ];
    }
}
