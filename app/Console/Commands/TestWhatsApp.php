<?php

namespace App\Console\Commands;

use App\Services\WhatsAppNotifikasi;
use Illuminate\Console\Command;

class TestWhatsApp extends Command
{
    protected $signature = 'absensi:wa-test
        {nomor : Nomor WhatsApp tujuan, mis. 081234567890}
        {--pesan= : Isi pesan (opsional)}';

    protected $description = 'Uji koneksi gateway WhatsApp (Fonnte) dengan mengirim satu pesan';

    public function handle(WhatsAppNotifikasi $gateway): int
    {
        $nomor = WhatsAppNotifikasi::normalisasiNomor($this->argument('nomor'));

        if ($nomor === null) {
            $this->error('Nomor tidak valid. Contoh format: 081234567890 atau 6281234567890.');

            return self::FAILURE;
        }

        $this->line("Driver : {$gateway->driver()}");
        $this->line('Status : '.($gateway->aktif() ? 'aktif (pesan benar-benar dikirim)' : 'mode uji (tidak mengirim)'));
        $this->line("Tujuan : {$nomor}");

        $pesan = $this->option('pesan')
            ?: "*UJI NOTIFIKASI*\nPesan percobaan dari aplikasi absensi sekolah.\nPukul: ".now()->format('H:i').' '.now()->format('d/m/Y');

        [$berhasil, $error] = $gateway->kirim($nomor, $pesan);

        if ($berhasil) {
            $this->info('Pesan berhasil dikirim.');

            return self::SUCCESS;
        }

        $this->error('Gagal mengirim: '.$error);

        return self::FAILURE;
    }
}
