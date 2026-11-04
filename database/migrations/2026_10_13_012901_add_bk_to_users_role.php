<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private const WITH_BK = "enum('admin','guru','wali_kelas','piket','orang_tua','bk')";

    private const WITHOUT_BK = "enum('admin','guru','wali_kelas','piket','orang_tua')";

    public function up(): void
    {
        // MySQL tidak bisa ALTER enum lewat schema builder tanpa doctrine/dbal.
        // Di sqlite (database testing) enum tidak native, jadi migration ini dilewati.
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        DB::statement('ALTER TABLE users MODIFY COLUMN role '.self::WITH_BK." NOT NULL DEFAULT 'guru'");
    }

    public function down(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        DB::statement('ALTER TABLE users MODIFY COLUMN role '.self::WITHOUT_BK." NOT NULL DEFAULT 'guru'");
    }
};
