<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Strategi migrasi aman untuk tabel activities yang SUDAH berisi data:
 *   1. tambah category_id sebagai NULLABLE (dengan foreign key + restrictOnDelete)
 *   2. isi (backfill) baris lama dengan kategori default "Lainnya"
 *   3. baru ubah category_id menjadi NOT NULL
 * Kalau langsung dibuat NOT NULL, migration gagal karena baris lama tidak punya nilai.
 */
return new class extends Migration
{
    public function up(): void
    {
        // 1. nullable dulu
        Schema::table('activities', function (Blueprint $table) {
            $table->foreignId('category_id')
                ->nullable()
                ->after('id')
                ->constrained()
                ->restrictOnDelete();
        });

        // 2. backfill baris lama
        if (DB::table('activities')->whereNull('category_id')->exists()) {
            $defaultId = DB::table('categories')->where('slug', 'lainnya')->value('id')
                ?? DB::table('categories')->insertGetId([
                    'name' => 'Lainnya',
                    'slug' => 'lainnya',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

            DB::table('activities')
                ->whereNull('category_id')
                ->update(['category_id' => $defaultId]);
        }

        // 3. wajib (NOT NULL)
        Schema::table('activities', function (Blueprint $table) {
            $table->unsignedBigInteger('category_id')->nullable(false)->change();
        });
    }

    public function down(): void
    {
        Schema::table('activities', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropColumn('category_id');
        });
    }
};
