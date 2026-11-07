<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Pengirim pesan WhatsApp ke orang tua murid.
 *
 * Driver dipilih lewat WA_DRIVER:
 *  - "log"    : tidak mengirim apa pun, hanya menulis ke laravel.log (mode uji).
 *  - "fonnte" : mengirim lewat https://api.fonnte.com/send memakai WA_TOKEN.
 */
class WhatsAppNotifikasi
{
    private string $driver;

    private ?string $token;

    private string $url;

    private int $timeout;

    public function __construct()
    {
        $this->driver = strtolower((string) config('services.whatsapp.driver', 'log'));
        $this->token = config('services.whatsapp.token');
        $this->url = (string) config('services.whatsapp.url', 'https://api.fonnte.com/send');
        $this->timeout = (int) config('services.whatsapp.timeout', 8);
    }

    public function driver(): string
    {
        return $this->driver;
    }

    /**
     * Notifikasi benar-benar dikirim hanya jika driver fonnte dan token terisi.
     */
    public function aktif(): bool
    {
        return $this->driver === 'fonnte' && filled($this->token);
    }

    /**
     * @return array{0: bool, 1: ?string} [berhasil, pesan error]
     */
    public function kirim(?string $nomor, string $pesan): array
    {
        $target = self::normalisasiNomor($nomor);

        if ($target === null) {
            return [false, 'Nomor WhatsApp orang tua tidak valid / kosong.'];
        }

        if ($this->driver === 'log') {
            Log::info("[WA simulasi] ke {$target}: {$pesan}");

            return [true, null];
        }

        if ($this->driver !== 'fonnte') {
            return [false, "Driver WhatsApp \"{$this->driver}\" tidak dikenal."];
        }

        if (! filled($this->token)) {
            return [false, 'WA_TOKEN belum diisi di .env.'];
        }

        try {
            $response = Http::withHeaders(['Authorization' => $this->token])
                ->asForm()
                ->timeout($this->timeout)
                ->post($this->url, [
                    'target' => $target,
                    'message' => $pesan,
                ]);
        } catch (Throwable $e) {
            report($e);

            return [false, 'Gagal menghubungi gateway: '.$e->getMessage()];
        }

        if ($response->failed()) {
            return [false, 'Gateway menolak (HTTP '.$response->status().').'];
        }

        // Fonnte membalas {"status": true/false, "reason": "..."}.
        $body = $response->json();

        if (is_array($body) && array_key_exists('status', $body) && $body['status'] === false) {
            return [false, 'Gateway menolak: '.($body['reason'] ?? 'alasan tidak diketahui').'.'];
        }

        return [true, null];
    }

    /**
     * Ubah nomor Indonesia ke format internasional tanpa "+" (62...).
     * Mengembalikan null jika tidak masuk akal.
     */
    public static function normalisasiNomor(?string $nomor): ?string
    {
        if ($nomor === null) {
            return null;
        }

        $digit = preg_replace('/\D+/', '', $nomor);

        if ($digit === null || $digit === '') {
            return null;
        }

        if (str_starts_with($digit, '0')) {
            $digit = '62'.substr($digit, 1);
        } elseif (str_starts_with($digit, '8')) {
            $digit = '62'.$digit;
        } elseif (str_starts_with($digit, '620')) {
            $digit = '62'.substr($digit, 3);
        }

        if (! str_starts_with($digit, '62')) {
            return null;
        }

        $panjang = strlen($digit);

        return ($panjang >= 10 && $panjang <= 15) ? $digit : null;
    }
}
