<?php

use App\Http\Controllers\Admin\GuruController;
use App\Http\Controllers\Admin\JadwalController;
use App\Http\Controllers\Admin\JurusanController;
use App\Http\Controllers\Admin\KelasController;
use App\Http\Controllers\Admin\PenggunaController;
use App\Http\Controllers\Admin\SiswaController;
use App\Http\Controllers\Piket\AbsensiController;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use App\Models\User;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;

require __DIR__.'/vendor/autoload.php';
$app = require __DIR__.'/bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

$admin = User::where('role', 'admin')->firstOrFail();
Auth::login($admin);
$req = fn (string $url, array $q = []) => tap(Request::create($url, 'GET', $q), fn ($r) => $r->setUserResolver(fn () => $admin));

$cek = function (string $nama, callable $fn, array $jarum = []) {
    try {
        $html = $fn();
        foreach ($jarum as $j) {
            if (! str_contains($html, $j)) {
                echo "GAGAL $nama: hilang '$j'\n";

                return;
            }
        }
        echo "OK $nama\n";
    } catch (Throwable $e) {
        echo 'GAGAL '.$nama.': '.substr($e->getMessage(), 0, 150)."\n";
    }
};

$cek('jurusan grid', fn () => (new JurusanController)->index($req('/x'))->render(), ['data-cari', 'x-model="cari"', 'Siswa']);
$cek('kelas grid', fn () => (new KelasController)->index($req('/x'))->render(), ['data-cari', 'Wali Kelas']);
$cek('pengguna grid', fn () => (new PenggunaController)->index($req('/x'))->render(), ['Tautkan anak', 'data-cari']);
$cek('guru grid', fn () => (new GuruController)->index($req('/x'))->render(), ['data-cari', 'jadwal']);
$cek('siswa rows', fn () => (new SiswaController)->index($req('/x'))->render(), ['Detail', 'admin/siswa/']);
$cek('jadwal tabs', fn () => (new JadwalController)->index($req('/x'))->render(), ['x-data="{ tab:', 'Senin', 'Selasa']);
$cek('gerbang bento', fn () => (app(AbsensiController::class))->index()->render(), ['Live Feed Hari Ini', 'live-feed-list', 'setInterval(muat, 10000)']);

// Detail siswa + QR SVG
$s = Siswa::firstOrFail();
$html = (new SiswaController)->show($req('/x'), $s->id)->render();
echo 'detail: '.(str_contains($html, '<svg') && str_contains($html, 'QR Kartu Pelajar') && str_contains($html, 'Riwayat Absensi Harian') ? 'OK ada QR+riwayat' : 'GAGAL')."\n";

// Feed endpoints
$r = Request::create('/x', 'GET', [], [], [], ['HTTP_ACCEPT' => 'application/json']);
$r->setUserResolver(fn () => $admin);
$res = (app(AbsensiController::class))->feed($r);
echo 'feed: '.get_class($res).', keys='.implode(',', array_keys($res->getData(true)))."\n";

// Foto upload via store controller (file gambar 1x1)
$tmp = tempnam(sys_get_temp_dir(), 'foto').'.png';
file_put_contents($tmp, base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg=='));
$req2 = Request::create('/x', 'POST', [
    'nisn' => 'FOTO01', 'nama_lengkap' => 'Uji Foto', 'jenis_kelamin' => 'L',
    'kelas_id' => Kelas::firstOrFail()->id, 'tahun_ajaran_id' => TahunAjaran::where('is_aktif', true)->firstOrFail()->id,
], [], ['foto' => new UploadedFile($tmp, 'foto.png', 'image/png', null, true)]);
$req2->setUserResolver(fn () => $admin);
(new SiswaController)->store($req2);
$su = Siswa::where('nisn', 'FOTO01')->firstOrFail();
echo 'foto tersimpan: '.($su->foto && file_exists(public_path($su->foto)) ? 'OK '.$su->foto : 'GAGAL')."\n";
(new SiswaController)->destroy($su->id);
echo 'foto ikut terhapus: '.(! file_exists(public_path($su->foto)) ? 'OK' : 'GAGAL')."\n";
