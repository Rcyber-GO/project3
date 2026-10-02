<?php

namespace App\Services;

use App\Models\Activity;
use App\Models\Category;
use Illuminate\Validation\ValidationException;

/**
 * Business rule dan transisi status kegiatan.
 * Transisi yang diizinkan (BR-06, BR-07):  draft -> published -> completed
 *
 * Refaktorisasi Task 3:
 *  - string literal status diganti konstanta Activity::STATUS_*
 *  - pemeriksaan kelengkapan dipecah: missingPublishFields() mengumpulkan kekurangan,
 *    ensureCompleteForPublish() hanya memutuskan melempar error atau tidak.
 */
class ActivityService
{
    public function createDraft(array $data): Activity
    {
        $activity = new Activity($data);
        $activity->status = Activity::STATUS_DRAFT;
        $activity->save();

        return $activity;
    }

    public function update(Activity $activity, array $data): Activity
    {
        $activity->update($data);

        return $activity;
    }

    public function publish(Activity $activity): Activity
    {
        $this->ensureStatusIs($activity, Activity::STATUS_DRAFT, 'Hanya kegiatan draft yang dapat dipublikasikan.');
        $this->ensureCompleteForPublish($activity);

        return $this->changeStatus($activity, Activity::STATUS_PUBLISHED);
    }

    public function complete(Activity $activity): Activity
    {
        $this->ensureStatusIs($activity, Activity::STATUS_PUBLISHED, 'Hanya kegiatan published yang dapat diselesaikan.');

        return $this->changeStatus($activity, Activity::STATUS_COMPLETED);
    }

    private function ensureStatusIs(Activity $activity, string $expected, string $message): void
    {
        if ($activity->status !== $expected) {
            throw ValidationException::withMessages(['status' => $message]);
        }
    }

    private function changeStatus(Activity $activity, string $target): Activity
    {
        $activity->status = $target;
        $activity->save();

        return $activity;
    }

    private function ensureCompleteForPublish(Activity $activity): void
    {
        $missing = $this->missingPublishFields($activity);

        if ($missing !== []) {
            throw ValidationException::withMessages([
                'status' => 'Kegiatan belum dapat dipublikasikan. Lengkapi dulu: '.implode(', ', $missing).'.',
            ]);
        }
    }

    /**
     * BR-05: daftar field wajib yang belum lengkap/valid saat publish.
     *
     * @return list<string>
     */
    private function missingPublishFields(Activity $activity): array
    {
        $checks = [
            'kategori' => $activity->category_id && Category::whereKey($activity->category_id)->exists(),
            'kode' => filled($activity->code),
            'judul' => filled($activity->title),
            'lokasi' => filled($activity->location),
            'tanggal mulai/selesai' => $activity->start_at && $activity->end_at && $activity->end_at->gte($activity->start_at),
            'kapasitas (1-500)' => $activity->capacity >= 1 && $activity->capacity <= 500,
        ];

        return array_keys(array_filter($checks, fn ($ok) => ! $ok));
    }
}
