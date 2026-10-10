<?php

namespace App\Services;

use App\Models\Activity;
use App\Models\Category;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

/**
 * Business rule, transisi status, dan penanganan poster kegiatan.
 * Transisi yang diizinkan (BR-06, BR-07):  draft -> published -> completed
 */
class ActivityService
{
    private const POSTER_DISK = 'public';
    private const POSTER_DIRECTORY = 'posters';

    public function createDraft(array $data, ?UploadedFile $poster = null): Activity
    {
        $activity = new Activity($data);
        $activity->status = Activity::STATUS_DRAFT;
        $activity->save();

        if ($poster) {
            $this->replacePoster($activity, $poster);
        }

        return $activity;
    }

    public function update(Activity $activity, array $data, ?UploadedFile $poster = null): Activity
    {
        $activity->update($data);

        if ($poster) {
            $this->replacePoster($activity, $poster);
        }

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

    /**
     * Simpan file baru dulu, catat path-nya di database, BARU hapus file lama.
     * Urutan ini mencegah kehilangan poster lama jika penyimpanan file baru gagal.
     * Soft delete kegiatan tidak memanggil method ini, sehingga poster tetap ada untuk restore.
     */
    private function replacePoster(Activity $activity, UploadedFile $poster): void
    {
        $oldPath = $activity->poster_path;
        $newPath = $poster->store(self::POSTER_DIRECTORY, self::POSTER_DISK);

        $activity->poster_path = $newPath;
        $activity->save();

        if ($oldPath && $oldPath !== $newPath) {
            Storage::disk(self::POSTER_DISK)->delete($oldPath);
        }
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
