<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->string('id_barang', 10)->primary();
            $table->string('nama_barang', 50);
            $table->text('deskripsi');
            $table->decimal('harga', 12, 2);
            $table->unsignedInteger('stok')->default(0);
            $table->string('gambar');
            $table->timestamps();
        });

        Schema::create('orders', function (Blueprint $table) {
            $table->string('id_order', 15)->primary();
            $table->string('id_user', 15);
            $table->dateTime('tanggal_order');
            $table->decimal('total_harga', 12, 2);
            $table->text('alamat_pengiriman');
            $table->timestamps();

            $table->foreign('id_user')->references('id_user')->on('users')->cascadeOnDelete();
        });

        Schema::create('order_details', function (Blueprint $table) {
            $table->string('id_order', 15);
            $table->string('id_barang', 10);
            $table->decimal('harga_satuan', 12, 2);
            $table->unsignedInteger('jumlah_beli');

            $table->primary(['id_order', 'id_barang']);
            $table->foreign('id_order')->references('id_order')->on('orders')->cascadeOnDelete();
            $table->foreign('id_barang')->references('id_barang')->on('products')->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_details');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('products');
    }
};
