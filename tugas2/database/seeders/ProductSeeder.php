<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            ['name' => 'Buku Tulis', 'price' => 5000, 'stock' => 20, 'image' => 'book'],
            ['name' => 'Pulpen', 'price' => 3000, 'stock' => 30, 'image' => 'pen'],
            ['name' => 'Penggaris', 'price' => 4000, 'stock' => 18, 'image' => 'ruler'],
            ['name' => 'Pensil 2B', 'price' => 2500, 'stock' => 25, 'image' => 'pencil'],
            ['name' => 'Penghapus', 'price' => 1500, 'stock' => 15, 'image' => 'eraser'],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
