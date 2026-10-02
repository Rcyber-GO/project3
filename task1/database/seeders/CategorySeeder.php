<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $names = ['Seminar', 'Workshop', 'Lomba', 'Lainnya'];

        foreach ($names as $name) {
            // updateOrCreate: aman dijalankan berulang tanpa membuat duplikat slug
            Category::updateOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name]
            );
        }
    }
}
