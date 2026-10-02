<?php

namespace Database\Seeders;

use App\Models\Activity;
use App\Models\Category;
use Illuminate\Database\Seeder;

/**
 * 18 kegiatan dari 3 kategori dan 3 status, untuk menguji search/filter/sort/pagination.
 * Aman dijalankan berulang (updateOrCreate berdasarkan code).
 */
class ActivitySeeder extends Seeder
{
    public function run(): void
    {
        $categories = Category::whereIn('slug', ['seminar', 'workshop', 'lomba'])
            ->orderBy('id')
            ->pluck('id', 'slug');

        if ($categories->count() < 3) {
            $this->command?->warn('Jalankan CategorySeeder dulu.');

            return;
        }

        $slugs = ['seminar', 'workshop', 'lomba'];
        $names = ['Seminar', 'Workshop', 'Lomba'];

        Activity::unguarded(function () use ($categories, $slugs, $names) {
            for ($i = 1; $i <= 18; $i++) {
                $index = ($i - 1) % 3;
                $status = match (true) {
                    $i <= 6 => 'completed',
                    $i <= 14 => 'published',
                    default => 'draft',
                };

                // completed: sudah lewat; published/draft: ke depan (agar bisa dipakai uji pendaftaran)
                $start = $status === 'completed'
                    ? now()->subDays(60 - $i * 3)->setTime(9, 0)
                    : now()->addDays($i * 3)->setTime(9, 0);

                Activity::updateOrCreate(
                    ['code' => sprintf('ACT-%03d', $i)],
                    [
                        'category_id' => $categories[$slugs[$index]],
                        'title' => $names[$index].' Kegiatan '.$i,
                        'description' => 'Data uji nomor '.$i,
                        'location' => 'Ruang '.(100 + $i),
                        'start_at' => $start,
                        'end_at' => (clone $start)->addHours(3),
                        'capacity' => 20 + $i,
                        'status' => $status,
                    ]
                );
            }
        });
    }
}
