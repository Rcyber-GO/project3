<?php

namespace App\Services;

use App\Models\Activity;
use App\Models\Registration;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/**
 * Pendaftaran peserta (Independent Challenge, aturan IC-01 s.d. IC-06).
 */
class RegistrationService
{
    public function register(Activity $activity, array $data): Registration
    {
        $email = Str::lower(trim($data['email']));

        // Hint 1: periksa rule dulu, terpisah dari penyimpanan.
        $this->assertCanRegister($activity, $email);

        try {
            // Hint 3: create Registration + increment registered_count = satu kesatuan (IC-05, IC-06).
            return DB::transaction(function () use ($activity, $data, $email) {
                $registration = $activity->registrations()->create([
                    'participant_name' => $data['participant_name'],
                    'email' => $email,
                    'registered_at' => now(),
                ]);

                $activity->increment('registered_count');

                return $registration;
            });
        } catch (UniqueConstraintViolationException) {
            // Hint 2: unique(activity_id, email) di database menangkap kasus balapan dua request.
            throw ValidationException::withMessages([
                'email' => 'Email ini sudah terdaftar pada kegiatan yang sama.',
            ]);
        }
    }

    private function assertCanRegister(Activity $activity, string $email): void
    {
        // IC-01
        if ($activity->status !== Activity::STATUS_PUBLISHED) {
            throw ValidationException::withMessages([
                'activity' => 'Pendaftaran hanya dibuka untuk kegiatan yang sudah dipublikasikan.',
            ]);
        }

        // IC-02
        if ($activity->start_at->isPast()) {
            throw ValidationException::withMessages([
                'activity' => 'Pendaftaran ditutup karena kegiatan sudah dimulai.',
            ]);
        }

        // IC-04
        if ($activity->registered_count >= $activity->capacity) {
            throw ValidationException::withMessages([
                'activity' => 'Kapasitas kegiatan sudah penuh.',
            ]);
        }

        // IC-03 (cek aplikasi; penjaga terakhir = unique index)
        if ($activity->registrations()->where('email', $email)->exists()) {
            throw ValidationException::withMessages([
                'email' => 'Email ini sudah terdaftar pada kegiatan yang sama.',
            ]);
        }
    }
}
