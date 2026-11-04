<?php

namespace Database\Seeders;

use App\Models\AnggotaKelas;
use App\Models\Guru;
use App\Models\JadwalPelajaran;
use App\Models\Jurusan;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\OrangTuaSiswa;
use App\Models\PengaturanAbsensi;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DummyDataSeeder extends Seeder
{
    private const TARGET = 100;

    /**
     * Data dummy untuk testing. Dijalankan manual:
     * php artisan db:seed --class=DummyDataSeeder
     * Aman dijalankan ulang (hanya menutup kekurangan sampai 100).
     */
    public function run(): void
    {
        $this->topUpTahunAjaran();
        $this->topUpPengguna();
        Jurusan::factory()->count($this->kurang(Jurusan::class))->create();
        Guru::factory()->count($this->kurang(Guru::class))->create();
        $this->tautkanAkunGuru();
        Kelas::factory()->count($this->kurang(Kelas::class))->create();
        $this->isiWaliKelas();
        $this->topUpSiswa();
        $this->topUpMapel();
        JadwalPelajaran::factory()->count($this->kurang(JadwalPelajaran::class))->create();
        $this->lengkapiPengaturan();
        $this->tautkanAnakOrtu();
    }

    private function kurang(string $model): int
    {
        return max(0, self::TARGET - $model::count());
    }

    private function tahunAktif(): ?TahunAjaran
    {
        return TahunAjaran::where('is_aktif', true)->first();
    }

    private function topUpTahunAjaran(): void
    {
        $ada = TahunAjaran::pluck('semester', 'tahun')->mapWithKeys(
            fn ($semester, $tahun) => ["$tahun-$semester" => true]
        )->all();

        $tahun = 1990;
        while (TahunAjaran::count() < self::TARGET && $tahun < 2100) {
            foreach (['1', '2'] as $semester) {
                if (TahunAjaran::count() >= self::TARGET) {
                    break;
                }
                $label = "$tahun/".($tahun + 1);
                if (! isset($ada["$label-$semester"])) {
                    TahunAjaran::create(['tahun' => $label, 'semester' => $semester, 'is_aktif' => false]);
                    $ada["$label-$semester"] = true;
                }
            }
            $tahun++;
        }
    }

    private function topUpPengguna(): void
    {
        $peran = array_merge(
            array_fill(0, 55, 'guru'),
            array_fill(0, 10, 'wali_kelas'),
            array_fill(0, 5, 'piket'),
            array_fill(0, 25, 'orang_tua'),
            array_fill(0, 5, 'admin'),
        );

        foreach ($peran as $i => $role) {
            if (User::count() >= self::TARGET) {
                break;
            }
            User::create([
                'name' => fake('id_ID')->name(),
                'username' => fake()->unique()->userName().$i,
                'email' => fake()->unique()->safeEmail(),
                'password' => Hash::make('password123'),
                'role' => $role,
                // Sebagian kecil guru diberi penugasan BK.
                'penugasan' => $role === 'guru' && $i % 10 === 0 ? 'bk' : null,
            ]);
        }
    }

    private function tautkanAkunGuru(): void
    {
        $akun = User::whereIn('role', ['guru', 'wali_kelas'])
            ->whereNotIn('id', Guru::whereNotNull('user_id')->pluck('user_id'))
            ->pluck('id');

        foreach (Guru::whereNull('user_id')->cursor() as $guru) {
            if ($akun->isEmpty()) {
                break;
            }
            $guru->update(['user_id' => $akun->shift()]);
        }
    }

    private function isiWaliKelas(): void
    {
        $guru = Guru::pluck('id');
        if ($guru->isEmpty()) {
            return;
        }

        foreach (Kelas::whereNull('wali_kelas_id')->cursor() as $kelas) {
            if (fake()->boolean(70)) {
                $kelas->update(['wali_kelas_id' => $guru->random()]);
            }
        }
    }

    private function topUpSiswa(): void
    {
        $aktif = $this->tahunAktif();
        $kelas = Kelas::pluck('id');
        if (! $aktif || $kelas->isEmpty()) {
            return;
        }

        Siswa::factory()->count($this->kurang(Siswa::class))->create()->each(
            fn ($siswa) => AnggotaKelas::create([
                'siswa_id' => $siswa->id,
                'kelas_id' => $kelas->random(),
                'tahun_ajaran_id' => $aktif->id,
            ])
        );
    }

    private function topUpMapel(): void
    {
        $jurusan = Jurusan::pluck('id');

        MataPelajaran::factory()->count($this->kurang(MataPelajaran::class))->create()->each(
            fn ($mapel) => $mapel->update([
                // 40% mapel jurusan, sisanya umum (null).
                'jurusan_id' => ! $jurusan->isEmpty() && fake()->boolean(40) ? $jurusan->random() : null,
            ])
        );
    }

    private function lengkapiPengaturan(): void
    {
        // Kunci pengaturan adalah nama hari: maksimal 6 baris, bukan 100.
        foreach (['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'] as $hari) {
            PengaturanAbsensi::firstOrCreate(['hari' => $hari], [
                'jam_masuk' => '07:00:00',
                'toleransi_keterlambatan' => 15,
                'jam_tutup_absensi' => '08:00:00',
                'jam_pulang' => '15:00:00',
                'durasi_absensi_pulang' => 60,
                'is_aktif' => true,
            ]);
        }
    }

    private function tautkanAnakOrtu(): void
    {
        $siswa = Siswa::pluck('id');
        if ($siswa->isEmpty()) {
            return;
        }

        foreach (User::where('role', 'orang_tua')->whereDoesntHave('orangTuaSiswa')->cursor() as $ortu) {
            foreach ($siswa->random(min(2, $siswa->count())) as $siswaId) {
                OrangTuaSiswa::firstOrCreate(
                    ['user_id' => $ortu->id, 'siswa_id' => $siswaId],
                    ['hubungan' => fake()->randomElement(['ayah', 'ibu', 'wali'])]
                );
            }
        }
    }
}
