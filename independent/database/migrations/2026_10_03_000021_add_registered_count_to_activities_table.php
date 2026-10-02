<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * registered_count sengaja didenormalisasi (modul 6.2) agar ada dua operasi database
 * yang harus konsisten: membuat Registration dan menambah registered_count.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('activities', 'registered_count')) {
            return;
        }

        Schema::table('activities', function (Blueprint $table) {
            $table->unsignedInteger('registered_count')->default(0);
        });
    }

    public function down(): void
    {
        if (Schema::hasColumn('activities', 'registered_count')) {
            Schema::table('activities', function (Blueprint $table) {
                $table->dropColumn('registered_count');
            });
        }
    }
};
