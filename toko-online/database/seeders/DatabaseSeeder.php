<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(['username' => 'budi'], [
            'id_user' => 'U00000000000001',
            'nama_lengkap' => 'Budi Santoso',
            'email' => 'budi@example.test',
            'password' => Hash::make('password123'),
            'no_hp' => '081234567890',
            'alamat' => 'Jl. Melati No. 10, Jakarta',
        ]);

        $products = [
            ['Buku Tulis', 'Buku tulis 38 lembar untuk kebutuhan sekolah.', 5000, 24],
            ['Pulpen Hitam', 'Pulpen tinta hitam nyaman digunakan.', 3000, 30],
            ['Penggaris 30 cm', 'Penggaris plastik bening sepanjang 30 cm.', 4000, 18],
            ['Pensil 2B', 'Pensil 2B untuk menulis dan menggambar.', 2500, 25],
            ['Penghapus Putih', 'Penghapus putih lembut, tidak merusak kertas.', 1500, 20],
            ['Spidol Warna', 'Spidol warna untuk menandai dan menggambar.', 8500, 12],
            ['Buku Gambar', 'Buku gambar dengan kertas tebal.', 7000, 14],
            ['Rautan Pensil', 'Rautan kecil dengan wadah serutan.', 3500, 16],
            ['Map Kertas', 'Map kertas untuk menyimpan dokumen.', 2000, 22],
            ['Stabilo Kuning', 'Stabilo warna kuning untuk menandai teks.', 6000, 10],
        ];

        foreach ($products as $index => [$name, $description, $price, $stock]) {
            Product::updateOrCreate(
                ['id_barang' => sprintf('P%08d', $index + 1)],
                [
                    'nama_barang' => $name,
                    'deskripsi' => $description,
                    'harga' => $price,
                    'stok' => $stock,
                    'gambar' => 'stationery.svg',
                ],
            );
        }
    }
}
