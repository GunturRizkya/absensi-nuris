<?php

namespace App\Jobs;

use App\Models\NotifikasiKehadiran;
use App\Services\WhatsAppNotifikasi;
use Illuminate\Foundation\Bus\Dispatchable;
use Throwable;

/**
 * Kirim notifikasi WhatsApp kehadiran ke orang tua.
 *
 * Job ini sengaja TIDAK mengimplementasikan ShouldQueue: dipanggil dengan
 * ->afterResponse() agar pengiriman WA tidak memperlambat scan di gerbang.
 */
class KirimNotifikasiWhatsApp
{
    use Dispatchable;

    public function __construct(
        public readonly int $siswaId,
        public readonly string $tanggal,
        public readonly string $jenis,
        public readonly ?string $nomor,
        public readonly string $pesan,
    ) {}

    public function handle(WhatsAppNotifikasi $gateway): void
    {
        try {
            [$berhasil, $error] = $gateway->kirim($this->nomor, $this->pesan);
        } catch (Throwable $e) {
            report($e);
            [$berhasil, $error] = [false, $e->getMessage()];
        }

        $catatan = $this->pesan;

        if (! $gateway->aktif()) {
            $catatan .= "\n\n_(mode uji — driver \"{$gateway->driver()}\", pesan tidak benar-benar dikirim)_";
        }

        if (! $berhasil) {
            $catatan .= "\n\n[gagal: {$error}]";
        }

        $log = NotifikasiKehadiran::firstOrNew([
            'siswa_id' => $this->siswaId,
            'tanggal' => $this->tanggal,
            'kanal' => 'wa',
        ]);

        // Satu baris per siswa+tanggal: masuk dan pulang digabung.
        $log->pesan = filled($log->pesan)
            ? trim($log->pesan)."\n\n".$catatan
            : $catatan;
        $log->status = $berhasil ? 'terkirim' : 'gagal';
        $log->save();
    }
}
