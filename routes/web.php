<?php

use App\Http\Controllers\Admin\GuruController;
use App\Http\Controllers\Admin\JadwalController;
use App\Http\Controllers\Admin\JurusanController;
use App\Http\Controllers\Admin\KelasController;
use App\Http\Controllers\Admin\MapelController;
use App\Http\Controllers\Admin\PengaturanAbsensiController;
use App\Http\Controllers\Admin\PenggunaController;
use App\Http\Controllers\Admin\RekapController;
use App\Http\Controllers\Admin\SiswaController;
use App\Http\Controllers\Admin\TahunAjaranController;
use App\Http\Controllers\BK\LaporanController as BKLaporanController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Guru\AbsensiMapelController as GuruAbsensiMapelController;
use App\Http\Controllers\OrangTua\NotifikasiController as OrtuNotifikasiController;
use App\Http\Controllers\OrangTua\RiwayatController as OrtuRiwayatController;
use App\Http\Controllers\OrangTua\SuratIzinController as OrtuSuratIzinController;
use App\Http\Controllers\OsisController;
use App\Http\Controllers\Piket\AbsensiController as PiketAbsensiController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WaliKelas\SuratIzinController as WaliSuratIzinController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('auth.login');
});

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {

    // Tahun Ajaran
    Route::get('/tahun-ajaran', [TahunAjaranController::class, 'index'])->name('tahun-ajaran.index');
    Route::post('/tahun-ajaran', [TahunAjaranController::class, 'store'])->name('tahun-ajaran.store');
    Route::patch('/tahun-ajaran/{id}/aktif', [TahunAjaranController::class, 'setAktif'])->name('tahun-ajaran.set-aktif');
    Route::delete('/tahun-ajaran/{id}', [TahunAjaranController::class, 'destroy'])->name('tahun-ajaran.destroy');
    
        // Hari Libur
    Route::get('/hari-libur', [\App\Http\Controllers\Admin\HariLiburController::class, 'index'])
        ->name('hari-libur.index');

    Route::post('/hari-libur', [\App\Http\Controllers\Admin\HariLiburController::class, 'store'])
        ->name('hari-libur.store');

    Route::put('/hari-libur/{id}', [\App\Http\Controllers\Admin\HariLiburController::class, 'update'])
        ->name('hari-libur.update');

    Route::delete('/hari-libur/{id}', [\App\Http\Controllers\Admin\HariLiburController::class, 'destroy'])
        ->name('hari-libur.destroy');

    // Pengaturan Jam Absensi
    Route::get('/pengaturan-absensi', [PengaturanAbsensiController::class, 'index'])->name('pengaturan-absensi.index');
    Route::put('/pengaturan-absensi/{id}', [PengaturanAbsensiController::class, 'update'])->name('pengaturan-absensi.update');

    // Jurusan
    Route::get('/jurusan', [JurusanController::class, 'index'])->name('jurusan.index');
    Route::post('/jurusan', [JurusanController::class, 'store'])->name('jurusan.store');
    Route::put('/jurusan/{id}', [JurusanController::class, 'update'])->name('jurusan.update');
    Route::delete('/jurusan/{id}', [JurusanController::class, 'destroy'])->name('jurusan.destroy');

    // Kelas
    Route::get('/kelas', [KelasController::class, 'index'])->name('kelas.index');
    Route::post('/kelas', [KelasController::class, 'store'])->name('kelas.store');
    Route::put('/kelas/{id}', [KelasController::class, 'update'])->name('kelas.update');
    Route::delete('/kelas/{id}', [KelasController::class, 'destroy'])->name('kelas.destroy');

    // Pengguna (akun login, role, penugasan)
    Route::get('/pengguna', [PenggunaController::class, 'index'])->name('pengguna.index');
    Route::post('/pengguna', [PenggunaController::class, 'store'])->name('pengguna.store');
    Route::put('/pengguna/{id}', [PenggunaController::class, 'update'])->name('pengguna.update');
    Route::delete('/pengguna/{id}', [PenggunaController::class, 'destroy'])->name('pengguna.destroy');
    Route::post('/pengguna/{id}/tautkan-anak', [PenggunaController::class, 'tautkanAnak'])->name('pengguna.tautkan-anak');
    Route::delete('/pengguna/lepas-anak/{pivotId}', [PenggunaController::class, 'lepasAnak'])->name('pengguna.lepas-anak');

    // Siswa + penempatan kelas (anggota_kelas)
    Route::get('/siswa', [SiswaController::class, 'index'])->name('siswa.index');
    Route::post('/siswa', [SiswaController::class, 'store'])->name('siswa.store');
    Route::get('/siswa/{id}', [SiswaController::class, 'show'])->name('siswa.show');
    Route::get('/siswa/{id}/kartu', [SiswaController::class, 'kartu'])->name('siswa.kartu');
    Route::put('/siswa/{id}', [SiswaController::class, 'update'])->name('siswa.update');
    Route::delete('/siswa/{id}', [SiswaController::class, 'destroy'])->name('siswa.destroy');

    // Guru (+ tautan akun user)
    Route::get('/guru', [GuruController::class, 'index'])->name('guru.index');
    Route::post('/guru', [GuruController::class, 'store'])->name('guru.store');
    Route::put('/guru/{id}', [GuruController::class, 'update'])->name('guru.update');
    Route::delete('/guru/{id}', [GuruController::class, 'destroy'])->name('guru.destroy');

    // Mata pelajaran
    Route::get('/mapel', [MapelController::class, 'index'])->name('mapel.index');
    Route::post('/mapel', [MapelController::class, 'store'])->name('mapel.store');
    Route::put('/mapel/{id}', [MapelController::class, 'update'])->name('mapel.update');
    Route::delete('/mapel/{id}', [MapelController::class, 'destroy'])->name('mapel.destroy');

    // Jadwal pelajaran
    Route::get('/jadwal', [JadwalController::class, 'index'])->name('jadwal.index');
    Route::post('/jadwal', [JadwalController::class, 'store'])->name('jadwal.store');
    Route::put('/jadwal/{id}', [JadwalController::class, 'update'])->name('jadwal.update');
    Route::delete('/jadwal/{id}', [JadwalController::class, 'destroy'])->name('jadwal.destroy');

    // Rekap admin
    Route::get('/rekap-harian', [RekapController::class, 'harian'])->name('rekap.harian');
    Route::get('/rekap-bulanan', [RekapController::class, 'bulanan'])->name('rekap.bulanan');
    Route::post('/rekap-auto-alpa', [RekapController::class, 'autoAlpa'])->name('rekap.auto-alpa');
    Route::post('/rekap-koreksi', [RekapController::class, 'koreksi'])->name('rekap.koreksi');
});

Route::middleware(['auth', 'role:bk'])->prefix('bk')->name('bk.')->group(function () {

    // Laporan BK: role guru dengan penugasan bk (lihat users.penugasan)
    Route::get('/laporan', [BKLaporanController::class, 'index'])->name('laporan.index');
    Route::get('/laporan/siswa/{siswa}', [BKLaporanController::class, 'show'])->name('laporan.show');
});

Route::middleware(['auth', 'role:piket,admin,osis'])->prefix('piket')->name('piket.')->group(function () {

    // Absensi gerbang harian: scan QR/RFID/sidik jari + input manual.
    Route::get('/absensi', [PiketAbsensiController::class, 'index'])->name('absensi.index');
    Route::get('/absensi/feed', [PiketAbsensiController::class, 'feed'])->name('absensi.feed');
    Route::post('/absensi/scan', [PiketAbsensiController::class, 'scan'])->name('absensi.scan');
    Route::post('/absensi/manual', [PiketAbsensiController::class, 'manual'])->name('absensi.manual');
    // Koreksi status memakai logika yang sama dengan admin.
    Route::post('/koreksi-absensi', [RekapController::class, 'koreksi'])->name('koreksi');
});

Route::middleware(['auth', 'role:osis'])->prefix('osis')->name('osis.')->group(function () {

    // Dashboard OSIS: ringkasan absensi gerbang hari ini + jalan pintas ke scan.
    Route::get('/dashboard', [OsisController::class, 'index'])->name('dashboard');
});

Route::middleware(['auth', 'role:wali_kelas,admin'])->prefix('wali-kelas')->name('walikelas.')->group(function () {

    // Persetujuan surat izin oleh wali kelas (guru yang ditunjuk) atau admin.
    Route::get('/surat-izin', [WaliSuratIzinController::class, 'index'])->name('surat-izin.index');
    Route::patch('/surat-izin/{id}/setujui', [WaliSuratIzinController::class, 'setujui'])->name('surat-izin.setujui');
    Route::patch('/surat-izin/{id}/tolak', [WaliSuratIzinController::class, 'tolak'])->name('surat-izin.tolak');
});

Route::middleware(['auth', 'role:guru,admin'])->prefix('guru')->name('guru.')->group(function () {

    // Absensi per mapel oleh guru pengampu (admin bebas mengisi).
    Route::get('/absensi-mapel', [GuruAbsensiMapelController::class, 'index'])->name('absensi-mapel.index');
    Route::get('/absensi-mapel/jadwal/{jadwal}', [GuruAbsensiMapelController::class, 'form'])->name('absensi-mapel.form');
    Route::post('/absensi-mapel/jadwal/{jadwal}', [GuruAbsensiMapelController::class, 'simpan'])->name('absensi-mapel.simpan');
});

Route::middleware(['auth', 'role:orang_tua'])->prefix('orang-tua')->name('orangtua.')->group(function () {
    // Portal orang tua: notifikasi aplikasi + riwayat + ajukan surat izin.
    Route::get('/notifikasi', [OrtuNotifikasiController::class, 'index'])->name('notifikasi.index');
    Route::patch('/notifikasi/baca-semua', [OrtuNotifikasiController::class, 'bacaSemua'])->name('notifikasi.baca-semua');
    Route::patch('/notifikasi/{id}/baca', [OrtuNotifikasiController::class, 'baca'])->name('notifikasi.baca');
    Route::get('/riwayat', [OrtuRiwayatController::class, 'index'])->name('riwayat.index');
    Route::get('/surat-izin', [OrtuSuratIzinController::class, 'index'])->name('surat-izin.index');
    Route::post('/surat-izin', [OrtuSuratIzinController::class, 'store'])->name('surat-izin.store');
});

require __DIR__.'/auth.php';
