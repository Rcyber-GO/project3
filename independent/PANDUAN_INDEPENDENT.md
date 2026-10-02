# Independent Challenge - Atomic Registration

Commit yang disarankan: `modul3-special-independent` (sesudah tag Task 3, sebelum Add-on)
Syarat: Task 1-3 sudah di-commit.

> Modul meminta Independent Challenge dikerjakan sendiri dengan hint bertahap dan dicatat di worksheet.
> Paket ini berisi satu solusi lengkap yang mengikuti Hint 1-4. Bacalah dan pahami sebelum
> demonstrasi, karena kamu harus bisa menjelaskan dan mengubah kodenya saat code defense.

## 1. Salin file
- `database/migrations/2026_10_03_000020_create_registrations_table.php` (baru)
- `database/migrations/2026_10_03_000021_add_registered_count_to_activities_table.php` (baru)
- `app/Models/Registration.php` (baru)
- `app/Models/Activity.php` (timpa: tambah relasi `registrations()` dan cast `registered_count`)
- `app/Services/RegistrationService.php` (baru)
- `app/Http/Requests/StoreRegistrationRequest.php` (baru)
- `app/Http/Controllers/RegistrationController.php` (baru)
- `resources/views/registrations/create.blade.php` (baru)

## 2. Route
```php
use App\Http\Controllers\RegistrationController;

Route::get('/activities/{activity}/register', [RegistrationController::class, 'create'])->name('registrations.create');
Route::post('/activities/{activity}/registrations', [RegistrationController::class, 'store'])->name('registrations.store');
```

## 3. View
Di `activities/show.blade.php` tampilkan jumlah pendaftar dan link daftar (untuk kegiatan published):
```blade
<p>Pendaftar: {{ $activity->registered_count }} / {{ $activity->capacity }}</p>
@if ($activity->status === 'published')
    <a href="{{ route('registrations.create', $activity) }}">Daftar</a>
@endif
```

## 4. Jalankan
```bash
php artisan migrate
```

## 5. Pemetaan aturan (IC) ke kode
| Aturan | Lokasi |
|--------|--------|
| IC-01 hanya published | `RegistrationService::assertCanRegister` |
| IC-02 ditolak jika start_at lewat | `RegistrationService::assertCanRegister` |
| IC-03 email unik per kegiatan | cek di service + `unique(['activity_id','email'])` di migration |
| IC-04 tidak melebihi capacity | `RegistrationService::assertCanRegister` |
| IC-05 Registration + registered_count satu transaction | `DB::transaction` di `register()` |
| IC-06 tidak ada data parsial jika increment gagal | rollback otomatis transaction |

## 6. Bukti wajib (screenshot)
Pakai kegiatan published di seeder (ACT-007 s.d. ACT-014).

1. **Pendaftaran valid berhasil:** daftar lewat `/activities/{id}/register`; pesan "Pendaftaran berhasil", `registered_count` bertambah 1; screenshot tabel `registrations` juga.
2. **Email duplikat ditolak:** daftar ulang dengan email sama pada kegiatan yang sama: pesan "Email ini sudah terdaftar...". Email sama boleh mendaftar di kegiatan LAIN.
3. **Kegiatan draft menolak:** buka `/activities/{id}/register` dari kegiatan draft (ACT-015..018) lalu submit: "hanya dibuka untuk kegiatan yang sudah dipublikasikan".
   (Tombol Daftar disembunyikan untuk draft, jadi ketik URL-nya langsung.)
4. **Kapasitas penuh menolak:**
   ```php
   App\Models\Activity::where('code', 'ACT-008')->update(['capacity' => 1]);
   ```
   Daftar sekali (berhasil), daftar lagi dengan email lain: "Kapasitas kegiatan sudah penuh".
5. **start_at lewat menolak (IC-02):**
   ```php
   App\Models\Activity::where('code', 'ACT-009')->update(['start_at' => now()->subDay(), 'end_at' => now()->subDay()->addHours(3)]);
   ```
   Daftar: "Pendaftaran ditutup karena kegiatan sudah dimulai".
6. **Rollback terkontrol (Hint 4).** Di tinker, gunakan kegiatan published yang masih punya kuota (mis. ACT-010):
   ```php
   $a = App\Models\Activity::where('code', 'ACT-010')->first();
   $before = [App\Models\Registration::count(), $a->registered_count];

   try {
       DB::transaction(function () use ($a) {
           $a->registrations()->create([
               'participant_name' => 'Uji Rollback',
               'email' => 'rollback@example.com',
               'registered_at' => now(),
           ]);

           throw new RuntimeException('Simulasi kegagalan sebelum increment');

           $a->increment('registered_count');
       });
   } catch (RuntimeException $e) {
       echo $e->getMessage();
   }

   $after = [App\Models\Registration::count(), $a->fresh()->registered_count];
   [$before, $after];   // keduanya sama: tidak ada Registration tertinggal
   ```
   Pembanding tanpa transaction (hanya untuk melihat bedanya, lalu bersihkan):
   ```php
   $a->registrations()->create(['participant_name'=>'Tanpa Trx','email'=>'parsial@example.com','registered_at'=>now()]);
   // lanjut ke langkah lain gagal -> Registration sudah tersimpan tetapi registered_count tidak bertambah
   App\Models\Registration::where('email','parsial@example.com')->delete();
   ```
7. **Penjelasan mengapa validation saja tidak menggantikan transaction:** validation hanya memeriksa kondisi
   SEBELUM penyimpanan. Setelah lolos, penyimpanan terdiri dari dua operasi (create dan increment). Kalau
   operasi kedua gagal (error database, koneksi putus, exception), operasi pertama sudah tersimpan sehingga
   data menjadi parsial (peserta tercatat tetapi hitungan salah). Transaction membuat keduanya satu
   kesatuan: gagal satu, batal semua (rollback). Validation mencegah input yang tidak boleh; transaction
   menjaga konsistensi saat proses multi-langkah gagal di tengah.

## 7. Catatan desain (untuk code defense)
- `registered_count` adalah denormalisasi sengaja untuk latihan. Pada sistem lain cukup
  `$activity->registrations()->count()`. Konsekuensinya: dua sumber kebenaran yang harus dijaga konsisten
  (itulah gunanya transaction).
- Race condition lanjutan (dua pendaftar terakhir bersamaan melewati cek kapasitas) memerlukan locking
  (`lockForUpdate`), yang berada di luar cakupan modul. Unique index tetap menangkap email ganda
  di level database; kodenya mengubah error itu menjadi pesan validasi.
- Email dinormalisasi ke huruf kecil agar `A@x.com` dan `a@x.com` tidak lolos sebagai dua email.

## 8. Commit
```bash
git add .
git commit -m "feat(independent): pendaftaran atomic dengan DB::transaction dan registered_count"
git tag modul3-special-independent
```
