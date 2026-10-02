<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * BR-02: kode kegiatan harus unik. Dijaga di dua lapisan:
 * Form Request (umpan balik ke pengguna) dan unique index (penjaga terakhir di database).
 *
 * Kalau baseline-mu SUDAH punya ->unique() pada kolom code, migration ini otomatis
 * dilewati (cek hasIndex). Kalau tabel sudah berisi kode duplikat, bersihkan dulu
 * datanya karena unique index tidak bisa dibuat.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasIndex('activities', 'activities_code_unique')) {
            return;
        }

        Schema::table('activities', function (Blueprint $table) {
            $table->unique('code');
        });
    }

    public function down(): void
    {
        if (Schema::hasIndex('activities', 'activities_code_unique')) {
            Schema::table('activities', function (Blueprint $table) {
                $table->dropUnique('activities_code_unique');
            });
        }
    }
};
