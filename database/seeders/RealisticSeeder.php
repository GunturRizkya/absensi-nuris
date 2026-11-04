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

/**
 * Data contoh REALISTIS secukupnya (SMK fiktif, nama Indonesia).
 * Dijalankan manual dengan database KOSONG:
 *   php artisan db:seed --class=RealisticSeeder
 *
 * Lawannya: DummyDataSeeder (100 baris acak per tabel untuk stress-test).
 */
class RealisticSeeder extends Seeder
{
    public function run(): void
    {
        $tahun = $this->tahunAjaran();
        $this->pengaturan();
        $jurusan = $this->jurusan();
        $kelas = $this->kelas($jurusan);
        $guru = $this->guruDanPengguna($kelas);
        $this->siswa($kelas, $tahun);
        $mapel = $this->mapel($jurusan);
        $this->jadwal($kelas, $mapel, $guru, $tahun);
        $this->orangTua();
    }

    private function tahunAjaran(): TahunAjaran
    {
        TahunAjaran::create(['tahun' => '2024/2025', 'semester' => '2', 'is_aktif' => false]);

        return TahunAjaran::create(['tahun' => '2025/2026', 'semester' => '1', 'is_aktif' => true]);
    }

    private function pengaturan(): void
    {
        $jadwal = [];
        $jadwal[] = ['Senin', '07:00:00', 15, '08:00:00', '15:00:00'];
        $jadwal[] = ['Selasa', '07:00:00', 15, '08:00:00', '15:00:00'];
        $jadwal[] = ['Rabu', '07:00:00', 15, '08:00:00', '15:00:00'];
        $jadwal[] = ['Kamis', '07:00:00', 15, '08:00:00', '15:00:00'];
        $jadwal[] = ['Jumat', '07:00:00', 15, '08:00:00', '14:00:00'];
        $jadwal[] = ['Sabtu', '07:00:00', 15, '08:00:00', '11:30:00'];

        foreach ($jadwal as $row) {
            PengaturanAbsensi::create([
                'hari' => $row[0],
                'jam_masuk' => $row[1],
                'toleransi_keterlambatan' => $row[2],
                'jam_tutup_absensi' => $row[3],
                'jam_pulang' => $row[4],
                'durasi_absensi_pulang' => 60,
                'is_aktif' => true,
            ]);
        }
    }

    private function jurusan(): array
    {
        $data = [
            ['RPL', 'Rekayasa Perangkat Lunak'],
            ['TKJ', 'Teknik Komputer dan Jaringan'],
            ['DKV', 'Desain Komunikasi Visual'],
        ];

        $keluar = [];
        foreach ($data as [$kode, $nama]) {
            $keluar[$kode] = Jurusan::create(['kode_jurusan' => $kode, 'nama_jurusan' => $nama]);
        }

        return $keluar;
    }

    private function kelas(array $jurusan): array
    {
        $daftar = [
            ['X RPL 1', 'X', 'RPL'], ['XI RPL 1', 'XI', 'RPL'], ['XII RPL 1', 'XII', 'RPL'],
            ['X TKJ 1', 'X', 'TKJ'], ['XI TKJ 1', 'XI', 'TKJ'], ['XII TKJ 1', 'XII', 'TKJ'],
            ['X DKV 1', 'X', 'DKV'], ['XI DKV 1', 'XI', 'DKV'], ['XII DKV 1', 'XII', 'DKV'],
        ];

        $keluar = [];
        foreach ($daftar as [$nama, $tingkat, $kode]) {
            $keluar[$nama] = Kelas::create([
                'jurusan_id' => $jurusan[$kode]->id,
                'nama_kelas' => $nama,
                'tingkat' => $tingkat,
            ]);
        }

        return $keluar;
    }

    /**
     * @return array<string, Guru> key = nama kelas yang diwalikan
     */
    private function guruDanPengguna(array $kelas): array
    {
        User::create([
            'name' => 'Administrator', 'username' => 'admin', 'email' => 'admin@smknuris.sch.id',
            'password' => Hash::make('password123'), 'role' => 'admin',
        ]);

        // [nama, nip, username, kelas yang diwalikan, penugasan]
        $daftar = [
            ['Ahmad Saepudin', '196805122000031001', 'ahmad.saepudin', 'X RPL 1', null],
            ['Siti Rahayu', '198203152005022001', 'siti.rahayu', 'XI RPL 1', null],
            ['Budi Santoso', '198911102015031002', 'budi.santoso', 'XII RPL 1', null],
            ['Dewi Lestari', '199005202018012003', 'dewi.lestari', 'X TKJ 1', 'bk'],
            ['Agus Wijaya', '198712032010011004', 'agus.wijaya', 'XI TKJ 1', null],
            ['Rina Marlina', '199208172020122005', 'rina.marlina', 'XII TKJ 1', null],
            ['Dadan Ramdani', '199511112022031006', 'dadan.ramdani', 'X DKV 1', null],
            ['Nina Kurnia', '199702142023012007', 'nina.kurnia', 'XI DKV 1', null],
            ['Yusuf Maulana', '199809302024011008', 'yusuf.maulana', 'XII DKV 1', null],
        ];

        $keluar = [];
        foreach ($daftar as [$nama, $nip, $username, $wali, $penugasan]) {
            $user = User::create([
                'name' => $nama, 'username' => $username,
                'email' => $username.'@smknuris.sch.id',
                'password' => Hash::make('password123'),
                'role' => 'guru', 'penugasan' => $penugasan,
            ]);
            $guru = Guru::create([
                'user_id' => $user->id, 'nip' => $nip, 'nama_lengkap' => $nama,
                'no_hp' => '0812'.fake()->numerify('########'),
            ]);
            $kelas[$wali]->update(['wali_kelas_id' => $guru->id]);
            $keluar[$nama] = $guru;
        }

        User::create([
            'name' => 'Petugas Piket', 'username' => 'piket', 'email' => 'piket@smknuris.sch.id',
            'password' => Hash::make('password123'), 'role' => 'piket',
        ]);

        User::create([
            'name' => 'Pengurus OSIS', 'username' => 'osis', 'email' => 'osis@smknuris.sch.id',
            'password' => Hash::make('password123'), 'role' => 'osis',
        ]);

        return $keluar;
    }

    private function siswa(array $kelas, TahunAjaran $tahun): void
    {
        // [nama, jenis kelamin, kelas] — 3-4 anak per kelas.
        $daftar = [
            ['Rizky Ramadhan', 'L', 'X RPL 1'], ['Putri Ayu Lestari', 'P', 'X RPL 1'], ['Fajar Nugroho', 'L', 'X RPL 1'], ['Intan Permata', 'P', 'X RPL 1'],
            ['Andi Prasetyo', 'L', 'XI RPL 1'], ['Sari Melati', 'P', 'XI RPL 1'], ['Dimas Saputra', 'L', 'XI RPL 1'],
            ['Bagus Setiawan', 'L', 'XII RPL 1'], ['Ayu Wandira', 'P', 'XII RPL 1'], ['Rudi Hartono', 'L', 'XII RPL 1'],
            ['Ilham Maulana', 'L', 'X TKJ 1'], ['Nadia Safitri', 'P', 'X TKJ 1'], ['Eko Purnomo', 'L', 'X TKJ 1'],
            ['Teguh Firmansyah', 'L', 'XI TKJ 1'], ['Lina Marlina', 'P', 'XI TKJ 1'], ['Wahyu Hidayat', 'L', 'XI TKJ 1'],
            ['Yoga Pratama', 'L', 'XII TKJ 1'], ['Desi Ratnasari', 'P', 'XII TKJ 1'], ['Hendra Gunawan', 'L', 'XII TKJ 1'],
            ['Farhan Aziz', 'L', 'X DKV 1'], ['Kirana Dewi', 'P', 'X DKV 1'], ['Salma Aulia', 'P', 'X DKV 1'],
            ['Reza Pahlevi', 'L', 'XI DKV 1'], ['Tiara Andini', 'P', 'XI DKV 1'], ['Galih Permana', 'L', 'XI DKV 1'],
            ['Daffa Alfarizi', 'L', 'XII DKV 1'], ['Zahra Nabila', 'P', 'XII DKV 1'], ['Irfan Hakim', 'L', 'XII DKV 1'],
        ];

        $nisn = 61234001;
        foreach ($daftar as [$nama, $jk, $namaKelas]) {
            $nisnStr = '006'.($nisn++);
            $siswa = Siswa::create([
                'nisn' => $nisnStr,
                'nama_lengkap' => $nama,
                'jenis_kelamin' => $jk,
                'no_hp_ortu' => '0813'.fake()->numerify('########'),
                'qr_code' => $nisnStr, // QR = NISN agar mudah dites scan
                'card_number' => null,
                'fingerprint_id' => null,
            ]);
            AnggotaKelas::create([
                'siswa_id' => $siswa->id,
                'kelas_id' => $kelas[$namaKelas]->id,
                'tahun_ajaran_id' => $tahun->id,
            ]);
        }
    }

    private function mapel(array $jurusan): array
    {
        $umum = [
            ['PAI', 'Pendidikan Agama Islam'], ['PPKN', 'Pendidikan Pancasila'], ['BIND', 'Bahasa Indonesia'],
            ['MTK', 'Matematika'], ['BING', 'Bahasa Inggris'], ['SEJ', 'Sejarah Indonesia'],
            ['PJOK', 'Pendidikan Jasmani'], ['INFO', 'Informatika'],
        ];
        $khusus = [
            ['PPLG', 'Pemrograman Dasar', 'RPL'], ['BD', 'Basis Data', 'RPL'],
            ['JARKOM', 'Jaringan Komputer', 'TKJ'], ['SISKOM', 'Sistem Komputer', 'TKJ'],
            ['DG', 'Desain Grafis', 'DKV'], ['FOTO', 'Fotografi', 'DKV'],
        ];

        $keluar = [];
        foreach ($umum as [$kode, $nama]) {
            $keluar[$kode] = MataPelajaran::create(['kode_mapel' => $kode, 'nama_mapel' => $nama, 'jurusan_id' => null]);
        }
        foreach ($khusus as [$kode, $nama, $jur]) {
            $keluar[$kode] = MataPelajaran::create(['kode_mapel' => $kode, 'nama_mapel' => $nama, 'jurusan_id' => $jurusan[$jur]->id]);
        }

        return $keluar;
    }

    private function jadwal(array $kelas, array $mapel, array $guru, TahunAjaran $tahun): void
    {
        $namaGuru = array_keys($guru);
        $jam = [['07:00', '08:30'], ['08:30', '10:00'], ['10:30', '12:00'], ['13:00', '14:30']];
        $kodeUmum = ['PAI', 'PPKN', 'BIND', 'MTK', 'BING', 'SEJ', 'PJOK', 'INFO'];
        $kodeJurusan = ['RPL' => ['PPLG', 'BD'], 'TKJ' => ['JARKOM', 'SISKOM'], 'DKV' => ['DG', 'FOTO']];

        foreach ($kelas as $namaKelas => $k) {
            $jur = explode(' ', $namaKelas)[1];
            $rotasi = array_merge($kodeUmum, $kodeJurusan[$jur], $kodeUmum);
            $i = 0;

            foreach (['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'] as $hari) {
                foreach ($jam as [$mulai, $selesai]) {
                    $kode = $rotasi[$i++ % count($rotasi)];
                    JadwalPelajaran::create([
                        'tahun_ajaran_id' => $tahun->id,
                        'kelas_id' => $k->id,
                        'mata_pelajaran_id' => $mapel[$kode]->id,
                        'guru_id' => $guru[$namaGuru[($i + $k->id) % count($namaGuru)]]->id,
                        'hari' => $hari,
                        'jam_mulai' => $mulai,
                        'jam_selesai' => $selesai,
                    ]);
                }
            }
        }
    }

    private function orangTua(): void
    {
        // Tautkan 2 akun ortu ke anak pertama tiap 2 kelas sebagai contoh.
        $contoh = [
            ['H. Mahmud', 'mahmud', 'ayah', 'Rizky Ramadhan', 'Putri Ayu Lestari'],
            ['Hj. Aminah', 'aminah', 'ibu', 'Ilham Maulana', 'Nadia Safitri'],
        ];

        foreach ($contoh as $baris) {
            [$nama, $username, $hubungan] = $baris;
            $user = User::create([
                'name' => $nama, 'username' => $username,
                'email' => $username.'@gmail.com',
                'password' => Hash::make('password123'), 'role' => 'orang_tua',
            ]);
            foreach (array_slice($baris, 3) as $namaAnak) {
                if ($siswa = Siswa::where('nama_lengkap', $namaAnak)->first()) {
                    OrangTuaSiswa::create(['user_id' => $user->id, 'siswa_id' => $siswa->id, 'hubungan' => $hubungan]);
                }
            }
        }
    }
}
