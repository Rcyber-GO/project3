<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('registrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('activity_id')->constrained();
            $table->string('participant_name');
            $table->string('email');
            $table->timestamp('registered_at');
            $table->timestamps();

            // IC-03: penjaga terakhir di database selain validasi aplikasi
            $table->unique(['activity_id', 'email']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('registrations');
    }
};
