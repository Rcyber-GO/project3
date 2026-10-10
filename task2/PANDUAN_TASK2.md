# Task 2 - Business Rule dan Query Experience

Checkpoint commit: `modul3-special-task2`
Syarat: Task 1 sudah di-commit dan jalan.

> File di zip ini adalah PENGGANTI LENGKAP untuk: `Activity.php`, `ActivityController.php`,
> `StoreActivityRequest.php`, `UpdateActivityRequest.php`. Kalau project-mu punya method/field
> tambahan di file-file itu, gabungkan manual. Nama kelas memakai konvensi Laravel standar.

## 1. Salin file
- `app/Models/Activity.php` (timpa)
- `app/Services/ActivityService.php` (baru; buat folder `app/Services` jika belum ada)
- `app/Http/Controllers/ActivityController.php` (timpa)
- `app/Http/Requests/StoreActivityRequest.php` dan `UpdateActivityRequest.php` (timpa)
- `database/seeders/ActivitySeeder.php` (baru)
- `resources/views/activities/_flash.blade.php`, `_filters.blade.php`, `_actions.blade.php` (baru)

## 2. Route (`routes/web.php`)
Tambahkan di dekat `Route::resource('activities', ...)`:
```php
Route::post('/activities/{activity}/publish', [ActivityController::class, 'publish'])->name('activities.publish');
Route::post('/activities/{activity}/complete', [ActivityController::class, 'complete'])->name('activities.complete');
```

## 3. Seeder
Di `DatabaseSeeder::run()`:
```php
$this->call([
    CategorySeeder::class,
    ActivitySeeder::class,
]);
```
Lalu `php artisan db:seed` (atau `migrate:fresh --seed`). Hasil: 18 kegiatan, 3 kategori, 3 status.

## 4. Ubah view
**Form create & edit:** HAPUS input/select `status` kalau ada (status tidak boleh dari form umum).
Pastikan ada input `location`, `start_at`, `end_at`, `capacity` (bawaan baseline).

**`index.blade.php`:**
```blade
@include('activities._flash')
@include('activities._filters')

{{-- tabel: tambahkan kolom Status, Kategori, Aksi --}}
@foreach ($activities as $activity)
    <tr>
        <td>{{ $activity->code }}</td>
        <td>{{ $activity->title }}</td>
        <td>{{ $activity->category->name }}</td>
        <td>{{ $activity->start_at->format('d M Y H:i') }}</td>
        <td>{{ $activity->status }}</td>
        <td>
            {{-- link show/edit/hapus bawaan baseline --}}
            @include('activities._actions', ['activity' => $activity])
        </td>
    </tr>
@endforeach

{{ $activities->links() }}
```
Kalau `links()` tampil terlalu besar karena styling Tailwind, tambahkan
`Paginator::useBootstrapFive();` atau `useBootstrap()` di `AppServiceProvider::boot()`
sesuai CSS yang dipakai project-mu.

**`show.blade.php`:** tambahkan `@include('activities._flash')`, tampilkan status,
dan `@include('activities._actions', ['activity' => $activity])`.

## 5. Pengujian (screenshot untuk kolom Bukti)

### Eksperimen 2 (validation dan business rule)
| Uji | Cara | Hasil yang diharapkan |
|-----|------|-----------------------|
| end_at < start_at | create dengan tanggal selesai lebih awal | Ditolak: "Tanggal selesai tidak boleh lebih awal..." |
| Kode duplikat saat create | pakai kode `ACT-001` | Ditolak: "Kode kegiatan sudah dipakai." |
| Update tanpa ganti kode | edit judul kegiatan saja | Berhasil (tidak dianggap duplikat diri sendiri) |
| Update dengan kode milik kegiatan lain | ganti kode ke `ACT-002` | Ditolak |

### Skenario tabel Task 2 (transisi status)
1. **Draft lengkap -> Published:** buka kegiatan draft (ACT-015..ACT-018), klik Publish. Berhasil.
2. **Draft tidak lengkap -> Published:** jalankan di tinker lalu klik Publish pada kegiatan itu:
   ```php
   App\Models\Activity::where('code', 'ACT-016')->update(['location' => '']);
   ```
   Hasil: ditolak dengan pesan "Lengkapi dulu: lokasi."
   (Form create mewajibkan semua field, jadi "tidak lengkap" disimulasikan lewat perubahan data
   di luar form. Itulah alasan service tetap memeriksa BR-05 saat publish.)
3. **Published -> Completed:** klik Complete pada kegiatan published. Berhasil.
4. **Completed -> Draft:** dua bukti:
   - Edit kegiatan completed lewat form, ubah request di DevTools dengan menambah `status=draft`
     (mis. tambah `<input name="status" value="draft">`). Status tetap `completed`
     karena `status` tidak ada di rules maupun `$fillable`.
   - Tinker: `app(App\Services\ActivityService::class)->publish(App\Models\Activity::where('status','completed')->first());`
     melempar `ValidationException` ("Hanya kegiatan draft yang dapat dipublikasikan.").
5. **Search + Category + Status:** contoh URL
   `/activities?search=Seminar&category_id=1&status=published&sort=latest`
6. **Pagination halaman 2:** dengan filter aktif (mis. `?status=published`, ada 8 data; atau
   hanya `?sort=oldest` untuk 18 data), klik halaman 2. Lihat URL tetap memuat `search/status/sort` + `page=2`.

Tambahan bukti sort: bandingkan urutan `?sort=latest` dan `?sort=oldest` (tanggal mulai di tabel).

## 6. Jawaban pertanyaan modul (untuk code defense)
1. **Mengapa status tidak diubah lewat form edit umum?** Mass assignment tidak menjelaskan transisi
   yang diizinkan, tidak melindungi invariant domain (mis. completed tidak boleh kembali), dan membuat
   rule tersebar di banyak tempat. Method khusus (`publish`, `complete`) memusatkan guard di satu tempat.
2. **Validation rule vs business rule:** validation rule = format/isi input (`required`, `date`,
   `after_or_equal`, `exists`, `unique`) di Form Request. Business rule = bergantung state/proses
   (draft saja yang boleh publish, kelengkapan BR-05 saat publish, completed tidak bisa mundur) di service.
3. **Mengapa filter di query database, bukan `Activity::all()`?** `all()` memuat semua baris ke memori PHP
   lalu memfilter; makin banyak data makin boros memori dan waktu. Query menyaring di database
   (memakai index) dan `paginate(10)` hanya mengambil 10 baris (`LIMIT/OFFSET`).
4. **Kapan local scope membantu dan kapan berlebihan?** Membantu saat kondisi query sama dipakai
   berulang atau mulai mengganggu keterbacaan controller (`search`, `inCategory`). Berlebihan jika
   dibuat untuk satu kondisi sederhana yang dipakai sekali, atau jika scope memuat business process
   (itu tempatnya service) sehingga model membengkak.

## 7. Commit
```bash
git add .
git commit -m "feat(task2): service publish/complete, search filter sort pagination, scope query"
git tag modul3-special-task2
```
