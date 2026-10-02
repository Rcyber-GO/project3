# Task 3 - Data Lifecycle dan Query Quality

Checkpoint commit: `modul3-special-task3`
Syarat: Task 1 dan Task 2 sudah di-commit dan jalan.

> File di zip ini adalah PENGGANTI LENGKAP untuk `Activity.php`, `ActivityService.php`,
> `ActivityController.php`, `CategoryController.php`. Gabungkan manual kalau project-mu punya tambahan.

## 1. Salin file
- `database/migrations/2026_10_03_000010_add_soft_deletes_to_activities_table.php` (baru)
- `app/Models/Activity.php` (timpa: tambah `SoftDeletes` dan konstanta status)
- `app/Services/ActivityService.php` (timpa: refaktor, pakai konstanta)
- `app/Http/Controllers/ActivityController.php` (timpa: `with('category')`, `trash()`, `restore()`)
- `app/Http/Controllers/CategoryController.php` (timpa: cek `withTrashed()`)
- `resources/views/activities/trash.blade.php` (baru)

## 2. Route (`routes/web.php`)
```php
Route::get('/trash/activities', [ActivityController::class, 'trash'])->name('activities.trash');
Route::post('/activities/{activity}/restore', [ActivityController::class, 'restore'])
    ->withTrashed()
    ->name('activities.restore');
```
Tambahkan link "Trash" di `index.blade.php`: `<a href="{{ route('activities.trash') }}">Trash</a>`.

## 3. Jalankan
```bash
php artisan migrate
```
Pastikan tombol hapus di index memakai form `DELETE` ke `activities.destroy` (bawaan CRUD baseline).

## 4. Eksperimen 4: soft delete dan restore
Di `php artisan tinker`, catat angka SEBELUM hapus:
```php
App\Models\Activity::count();
App\Models\Activity::withTrashed()->count();
App\Models\Activity::onlyTrashed()->count();
```
Lalu hapus satu kegiatan draft lewat tombol Hapus di UI dan ulangi tiga perintah di atas.
Lalu klik Restore di halaman Trash dan ulangi lagi.

Isi tabel Eksperimen 4 (contoh dengan 18 data):
| Pemeriksaan | Sebelum Delete | Sesudah Soft Delete | Sesudah Restore |
|-------------|----------------|---------------------|-----------------|
| Tampil pada index | Ya | Tidak | Ya |
| Tersedia di onlyTrashed | Tidak | Ya | Tidak |
| Record tetap ada di DB | Ya | Ya (deleted_at terisi) | Ya (deleted_at NULL) |
`count()`: 18 -> 17 -> 18; `withTrashed()->count()`: 18 -> 18 -> 18; `onlyTrashed()->count()`: 0 -> 1 -> 0.
Screenshot juga isi tabel `activities` (kolom `deleted_at`) lewat DB client.

**forceDelete:** seharusnya dipakai hanya oleh administrator untuk pembersihan permanen (data uji
rusak, permintaan penghapusan data, atau retensi habis). Tidak diberikan sebagai aksi utama di UI
karena tidak bisa dibatalkan dan menghilangkan jejak audit serta kemungkinan restore.

## 5. Eksperimen 5: bukti N+1 (screenshot hasil `count`)
Jalankan di tinker (data sama: 10 kegiatan pertama).

Percobaan A, lazy loading pada loop:
```php
DB::flushQueryLog();
DB::enableQueryLog();
$activities = App\Models\Activity::take(10)->get();
foreach ($activities as $activity) { $activity->category?->name; }
count(DB::getQueryLog());   // diharapkan 11 (1 daftar + 10 kategori)
```
Percobaan B, eager loading:
```php
DB::flushQueryLog();
DB::enableQueryLog();
$activities = App\Models\Activity::with('category')->take(10)->get();
foreach ($activities as $activity) { $activity->category?->name; }
count(DB::getQueryLog());   // diharapkan 2 (1 daftar + 1 kategori WHERE IN)
```
Catatan: pastikan baris di percobaan A tidak sudah tereager-load (jalankan sesi tinker baru).
Kalau kamu ingin bukti di halaman index asli, tambahkan sementara di route/controller
`DB::enableQueryLog()` dan dump `count(DB::getQueryLog())` setelah query. HAPUS lagi sebelum commit.

Mengapa eager load semua relasi tanpa kebutuhan bukan kebiasaan baik: setiap relasi menambah query dan
memuat data ke memori walau tidak dipakai view. Eager load hanya relasi yang benar-benar ditampilkan
(di index: `category` saja).

## 6. Acceptance criteria Task 3
| AC | Cara membuktikan |
|----|------------------|
| AC-10 Soft delete | Hapus kegiatan, hilang dari index, ada di Trash, `deleted_at` terisi di DB |
| AC-11 Restore | Klik Restore, kegiatan kembali di index |
| AC-12 Tidak ada N+1 | Perbandingan 11 vs 2 query di atas |
| AC-13 Struktur tanggung jawab | Controller hanya orkestrasi; rule di `ActivityService`; query di scope model; view tanpa rule |
| AC-14 Refaktorisasi berbasis static analysis | Bagian 7 |

Cek tambahan: hapus kategori yang kegiatannya hanya ada di Trash. Harus ditolak dengan pesan
"termasuk yang ada di Trash" (inilah alasan `CategoryController` memakai `withTrashed()`).

Catatan unique: `code` tetap unik walau kegiatannya ada di Trash (unique index mencakup baris
soft-deleted). Artinya kode kegiatan yang di-trash tidak bisa dipakai ulang sebelum di-restore atau
dihapus permanen. Ini konsisten antara Form Request dan database.

## 7. SonarQube dan refaktorisasi
1. Pastikan fitur Task 1-3 berjalan normal.
2. Jalankan analisis sesuai konfigurasi kelas, contoh (sesuaikan key/host/token dari dosen):
   ```bash
   sonar-scanner -Dsonar.projectKey=<key> -Dsonar.sources=app,database,routes,resources \
     -Dsonar.host.url=<url> -Dsonar.token=<token>
   ```
3. Pilih minimal 3 temuan di `app/` (bukan `vendor/`), kelompokkan: relevan / perlu konteks / tidak relevan.
4. Perbaiki minimal satu temuan relevan, uji ulang skenario terkait, catat before/after.

Tiga refaktorisasi yang sudah disiapkan di kode ini (cocokkan dengan temuan SonarQube-mu yang
SEBENARNYA; temuan yang dilaporkan SonarQube di project-mu bisa berbeda, dan yang dinilai adalah
temuan nyata dari hasil scan-mu):

| Temuan/kode smell | Before (Task 2) | After (Task 3) | Uji ulang |
|-------------------|-----------------|----------------|-----------|
| Magic string status `'draft'`, `'published'`, `'completed'` berulang di service, scope, controller | literal tersebar | konstanta `Activity::STATUS_*` dan `Activity::STATUSES` | AC-05: publish/complete/transisi ilegal |
| Variabel `$e` pada `catch (QueryException $e)` tidak dipakai | `catch (QueryException $e)` | `catch (QueryException)` | Hapus kategori yang dipakai (AC-03) |
| Method `ensureCompleteForPublish()` memuat banyak `if` (kompleksitas tinggi) | enam `if` berantai | `missingPublishFields()` mengumpulkan kekurangan, `ensureCompleteForPublish()` hanya memutuskan | Publish draft lengkap dan tidak lengkap (AC-04) |

Setelah refaktor, jalankan ulang skenario Task 1 dan Task 2 untuk memastikan perilaku tidak berubah,
lalu jalankan SonarQube lagi dan screenshot hasil sebelum/sesudah.

## 8. Checklist clean code (modul 8.2)
Tidak ada eksperimen sementara tersisa di controller/route; error ditampilkan lewat `_flash`;
Blade hanya menampilkan data; nama method (`publish`, `complete`, `restore`) menggambarkan tindakan.

## 9. Commit
```bash
git add .
git commit -m "feat(task3): soft delete, trash dan restore, eager loading, refaktor status constant"
git tag modul3-special-task3
```
