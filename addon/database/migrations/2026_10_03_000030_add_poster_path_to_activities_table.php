<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * poster_path menyimpan PATH file (mis. posters/abc.jpg), bukan isi file.
 * Dilewati otomatis jika baseline sudah punya kolom ini.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('activities', 'poster_path')) {
            return;
        }

        Schema::table('activities', function (Blueprint $table) {
            $table->string('poster_path')->nullable();
        });
    }

    public function down(): void
    {
        if (Schema::hasColumn('activities', 'poster_path')) {
            Schema::table('activities', function (Blueprint $table) {
                $table->dropColumn('poster_path');
            });
        }
    }
};
