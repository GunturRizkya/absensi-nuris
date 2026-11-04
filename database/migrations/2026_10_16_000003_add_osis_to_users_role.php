<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Dilewati di sqlite (database testing) karena sintaks khusus MySQL.
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        DB::statement("ALTER TABLE users MODIFY COLUMN role enum('admin','guru','wali_kelas','piket','orang_tua','osis') NOT NULL DEFAULT 'guru'");
    }

    public function down(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        DB::statement("ALTER TABLE users MODIFY COLUMN role enum('admin','guru','wali_kelas','piket','orang_tua') NOT NULL DEFAULT 'guru'");
    }
};
