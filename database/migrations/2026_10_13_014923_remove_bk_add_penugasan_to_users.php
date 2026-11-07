<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // BK bukan role tersendiri, melainkan penugasan pada user role guru.
        // Kembalikan enum role seperti semula.
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY COLUMN role enum('admin','guru','wali_kelas','piket','orang_tua') NOT NULL DEFAULT 'guru'");
        }

        Schema::table('users', function (Blueprint $table) {
            // Contoh nilai: 'bk'. NULL = tanpa penugasan khusus.
            $table->string('penugasan', 30)->nullable()->after('role');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('penugasan');
        });

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY COLUMN role enum('admin','guru','wali_kelas','piket','orang_tua','bk') NOT NULL DEFAULT 'guru'");
        }
    }
};
