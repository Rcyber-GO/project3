<?php

namespace App\Services;

use App\Models\Activity;
use App\Models\Category;
use Illuminate\Validation\ValidationException;

/**
 * Menampung business rule dan transisi status kegiatan.
 * Controller hanya memanggil method di sini (orchestrator), Blade tidak berisi rule.
 *
 * Transisi yang diizinkan (BR-06, BR-07):  draft -> published -> completed
 */
class ActivityService
{
    public function createDraft(array $data): Activity
    {
        $activity = new Activity($data);
        $activity->status = 'draft';   // status awal selalu draft, tidak dari input form
        $activity->save();

        return $activity;
    }

    public function update(Activity $activity, array $data): Activity
    {
        // status tidak ada di $fillable, jadi tidak bisa ikut berubah dari form edit
        $activity->update($data);

        return $activity;
    }

    public function publish(Activity $activity): Activity
    {
        if ($activity->status !== 'draft') {
            throw ValidationException::withMessages([
                'status' => 'Hanya kegiatan draft yang dapat dipublikasikan.',
            ]);
        }

        $this->ensureCompleteForPublish($activity);

        $activity->status = 'published';
        $activity->save();

        return $activity;
    }

    public function complete(Activity $activity): Activity
    {
        if ($activity->status !== 'published') {
            throw ValidationException::withMessages([
                'status' => 'Hanya kegiatan published yang dapat diselesaikan.',
            ]);
        }

        $activity->status = 'completed';
        $activity->save();

        return $activity;
    }

    /**
     * BR-05: kategori, kode, judul, lokasi, tanggal, dan kapasitas harus lengkap dan valid.
     * Form Request hanya memeriksa input saat disimpan; pemeriksaan ini memastikan
     * kondisi data SAAT publish (misalnya data diubah lewat jalur lain).
     */
    private function ensureCompleteForPublish(Activity $activity): void
    {
        $missing = [];

        if (! $activity->category_id || ! Category::whereKey($activity->category_id)->exists()) {
            $missing[] = 'kategori';
        }
        if (blank($activity->code)) {
            $missing[] = 'kode';
        }
        if (blank($activity->title)) {
            $missing[] = 'judul';
        }
        if (blank($activity->location)) {
            $missing[] = 'lokasi';
        }
        if (! $activity->start_at || ! $activity->end_at || $activity->end_at->lt($activity->start_at)) {
            $missing[] = 'tanggal mulai/selesai';
        }
        if (! $activity->capacity || $activity->capacity < 1 || $activity->capacity > 500) {
            $missing[] = 'kapasitas (1-500)';
        }

        if ($missing !== []) {
            throw ValidationException::withMessages([
                'status' => 'Kegiatan belum dapat dipublikasikan. Lengkapi dulu: '.implode(', ', $missing).'.',
            ]);
        }
    }
}
