<?php

namespace Tests\Feature;

use App\Jobs\KirimNotifikasiWhatsApp;
use App\Models\NotifikasiKehadiran;
use App\Models\PengaturanAbsensi;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use App\Models\User;
use App\Services\NotifikasiOrtu;
use App\Services\WhatsAppNotifikasi;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class WhatsAppNotifikasiTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_nomor_indonesia_dinormalisasi_ke_format_62(): void
    {
        $kasus = [
            '081234567890' => '6281234567890',
            '81234567890' => '6281234567890',
            '+62 812-3456-7890' => '6281234567890',
            '62081234567890' => '6281234567890',
            '6281234567890' => '6281234567890',
        ];

        foreach ($kasus as $input => $harapan) {
            $this->assertSame($harapan, WhatsAppNotifikasi::normalisasiNomor($input), "Gagal untuk {$input}");
        }

        foreach ([null, '', 'abc', '12345', '0812'] as $tidakValid) {
            $this->assertNull(WhatsAppNotifikasi::normalisasiNomor($tidakValid));
        }
    }

    public function test_driver_fonnte_mengirim_lewat_api(): void
    {
        config([
            'services.whatsapp.driver' => 'fonnte',
            'services.whatsapp.token' => 'token-rahasia',
            'services.whatsapp.url' => 'https://api.fonnte.com/send',
        ]);

        Http::fake([
            'api.fonnte.com/*' => Http::response(['status' => true, 'detail' => 'success'], 200),
        ]);

        $gateway = new WhatsAppNotifikasi;

        $this->assertTrue($gateway->aktif());
        $this->assertSame([true, null], $gateway->kirim('081234567890', 'Halo orang tua'));

        Http::assertSent(fn (Request $request) => $request->url() === 'https://api.fonnte.com/send'
            && $request['target'] === '6281234567890'
            && $request['message'] === 'Halo orang tua'
            && $request->hasHeader('Authorization', 'token-rahasia'));
    }

    public function test_gateway_menolak_dilaporkan_sebagai_gagal(): void
    {
        config([
            'services.whatsapp.driver' => 'fonnte',
            'services.whatsapp.token' => 'token-rahasia',
        ]);

        Http::fake([
            'api.fonnte.com/*' => Http::response(['status' => false, 'reason' => 'token tidak valid'], 200),
        ]);

        [$berhasil, $error] = (new WhatsAppNotifikasi)->kirim('081234567890', 'Halo');

        $this->assertFalse($berhasil);
        $this->assertStringContainsString('token tidak valid', $error);
    }

    public function test_driver_log_tidak_mengirim_http(): void
    {
        config(['services.whatsapp.driver' => 'log', 'services.whatsapp.token' => null]);

        Http::fake();

        $gateway = new WhatsAppNotifikasi;

        $this->assertFalse($gateway->aktif());
        $this->assertSame([true, null], $gateway->kirim('081234567890', 'Halo'));

        Http::assertNothingSent();
    }

    public function test_job_mencatat_satu_baris_wa_untuk_masuk_dan_pulang(): void
    {
        config(['services.whatsapp.driver' => 'log', 'services.whatsapp.token' => null]);

        $siswa = $this->buatSiswa();
        $gateway = app(WhatsAppNotifikasi::class);

        (new KirimNotifikasiWhatsApp($siswa->id, '2026-11-02', 'masuk', '6281234567890', 'Ananda masuk'))
            ->handle($gateway);
        (new KirimNotifikasiWhatsApp($siswa->id, '2026-11-02', 'pulang', '6281234567890', 'Ananda pulang'))
            ->handle($gateway);

        $log = NotifikasiKehadiran::where('siswa_id', $siswa->id)->where('kanal', 'wa')->get();

        $this->assertCount(1, $log);
        $this->assertSame('2026-11-02', substr((string) $log->first()->tanggal, 0, 10));
        $this->assertSame('terkirim', $log->first()->status);
        $this->assertStringContainsString('Ananda masuk', $log->first()->pesan);
        $this->assertStringContainsString('Ananda pulang', $log->first()->pesan);
        $this->assertStringContainsString('mode uji', $log->first()->pesan);
    }

    public function test_notifikasi_ortu_mengantre_wa_setelah_response(): void
    {
        Bus::fake();

        $siswa = $this->buatSiswa();

        NotifikasiOrtu::waMasuk($siswa, 'hadir', '07:05');

        Bus::assertDispatchedAfterResponse(KirimNotifikasiWhatsApp::class, fn (KirimNotifikasiWhatsApp $job) => $job->siswaId === $siswa->id
            && $job->nomor === '6281234567890'
            && $job->jenis === 'masuk'
            && str_contains($job->pesan, 'ABSENSI MASUK'));
    }

    public function test_siswa_tanpa_nomor_ortu_tidak_mengantre_wa(): void
    {
        Bus::fake();

        $siswa = $this->buatSiswa(['no_hp_ortu' => null]);

        NotifikasiOrtu::waMasuk($siswa, 'hadir', '07:05');

        Bus::assertNothingDispatched();
    }

    public function test_scan_masuk_dan_pulang_memicu_notifikasi_wa(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-11-02 07:05:00'));

        TahunAjaran::create(['tahun' => '2026/2027', 'semester' => '1', 'is_aktif' => true]);
        PengaturanAbsensi::create([
            'hari' => 'Senin',
            'jam_masuk' => '07:00:00',
            'toleransi_keterlambatan' => 15,
            'jam_tutup_absensi' => '09:00:00',
            'jam_pulang' => '15:00:00',
            'durasi_absensi_pulang' => 60,
            'is_aktif' => true,
        ]);

        $siswa = $this->buatSiswa(['qr_code' => 'QR-BUDI']);
        $user = User::factory()->create(['role' => 'piket']);

        Bus::fake();

        $this->actingAs($user)
            ->postJson(route('piket.absensi.scan'), ['kode' => 'QR-BUDI', 'metode' => 'qr'])
            ->assertOk()
            ->assertJsonPath('ok', true);

        Bus::assertDispatchedAfterResponse(KirimNotifikasiWhatsApp::class, fn (KirimNotifikasiWhatsApp $job) => $job->jenis === 'masuk'
            && str_contains($job->pesan, 'ABSENSI MASUK')
            && str_contains($job->pesan, 'HADIR'));

        $this->actingAs($user)
            ->post(route('piket.absensi.manual'), ['siswa_id' => $siswa->id, 'tipe' => 'pulang'])
            ->assertRedirect();

        Bus::assertDispatchedAfterResponse(KirimNotifikasiWhatsApp::class, fn (KirimNotifikasiWhatsApp $job) => $job->jenis === 'pulang'
            && str_contains($job->pesan, 'ABSENSI PULANG'));

        $this->assertDatabaseHas('notifikasi_kehadiran', [
            'siswa_id' => $siswa->id,
            'kanal' => 'aplikasi',
        ]);
    }

    private function buatSiswa(array $atribut = []): Siswa
    {
        return Siswa::create(array_merge([
            'nisn' => '1234567890',
            'nama_lengkap' => 'Budi Santoso',
            'jenis_kelamin' => 'L',
            'no_hp_ortu' => '081234567890',
        ], $atribut));
    }
}
