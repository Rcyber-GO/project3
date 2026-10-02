# Add-On - Poster Storage dan Refactoring

Commit akhir: `modul3-special-final` (terpisah dari checkpoint Task 1-3)
Syarat: Task 1-3 dan Independent Challenge sudah di-commit.

## 1. Salin file
- `database/migrations/2026_10_03_000030_add_poster_path_to_activities_table.php` (baru; otomatis dilewati jika `poster_path` sudah ada)
- `app/Http/Requests/StoreActivityRequest.php` (timpa: tambah rule `poster`; `UpdateActivityRequest` otomatis ikut karena meng-extend)
- `app/Services/ActivityService.php` (timpa: `createDraft/update` menerima poster, ada `replacePoster`)
- `app/Http/Controllers/ActivityController.php` (timpa: `store/update` meneruskan file poster)
- `resources/views/activities/_poster_input.blade.php` dan `_poster.blade.php` (baru)

## 2. Persiapan
```bash
php artisan migrate
php artisan storage:link
```
Cek `upload_max_filesize` di `php.ini` minimal 2M (default biasanya 2M; kalau file 2 MB persis gagal, naikkan sedikit).

## 3. Ubah view
**`create.blade.php` dan `edit.blade.php`:** tambahkan `enctype` pada tag form dan sertakan input poster:
```blade
<form action="..." method="POST" enctype="multipart/form-data">
    @csrf
    {{-- ...field lain... --}}
    @include('activities._poster_input')
    ...
</form>
```
Di edit tetap ada `@method('PUT')`.

**`show.blade.php`:** `@include('activities._poster', ['activity' => $activity])`

## 4. Pengujian (screenshot untuk worksheet bagian H)
| Pemeriksaan | Cara | Hasil |
|-------------|------|-------|
| Poster valid diunggah dan ditampilkan | create dengan file JPG/PNG < 2 MB | poster tampil di show; file ada di `storage/app/public/posters`; kolom `poster_path` berisi path, bukan isi file |
| File tidak sesuai ditolak | upload `.pdf` atau `.txt`; lalu gambar > 2 MB | "Poster harus berupa file gambar" / "Ukuran poster maksimal 2 MB" |
| Ganti poster tidak meninggalkan file lama | edit, upload poster baru | file lama hilang dari `storage/app/public/posters`, hanya file baru ada (lihat folder sebelum/sesudah) |
| Soft delete tidak merusak restore poster | hapus kegiatan yang punya poster, cek file masih ada, restore, poster tampil lagi | file tetap ada selama di Trash |

## 5. Kebijakan force delete (modul 7.1 butir 7)
Implementasi tidak wajib. Kebijakan yang dituliskan: saat administrator di masa mendatang menjalankan
`forceDelete()` pada kegiatan, file poster pada disk `public` ikut dihapus karena tidak ada lagi
kemungkinan restore. Soft delete tidak pernah menghapus file. Jika nanti diimplementasikan, tempatnya
di model:
```php
protected static function booted(): void
{
    static::forceDeleting(function (Activity $activity) {
        if ($activity->poster_path) {
            Storage::disk('public')->delete($activity->poster_path);
        }
    });
}
```
(opsional, tidak dipasang pada paket ini).

## 6. Refactoring review (modul 7.2) - bahan jawaban
| Pemeriksaan | Jawaban singkat |
|-------------|-----------------|
| Controller | Hanya orkestrasi: menerima request, memanggil service/scope, mengembalikan response. Rule status dan poster ada di service. |
| Form Request | Validasi input terpusat, termasuk aturan poster dan pesan bahasa Indonesia. Update meng-extend Store sehingga rule tidak duplikat. |
| Service | `publish`, `complete`, `replacePoster` bernama spesifik, tiap method punya satu tujuan. |
| Model | Relationship (`category`, `registrations`) dan query scope (`search`, `inCategory`, `withStatus`, `sortByStart`) ada di model; business process tidak. |
| Blade | Hanya menampilkan data; tidak ada query atau rule (partial `_actions`, `_poster`). |
| Penamaan | Tidak ada nama generik seperti `processData`. |
| Duplikasi | Filter query dalam scope, rule update memakai `parent::rules()`, status memakai konstanta. |

Refaktorisasi keterbacaan yang dijelaskan di worksheet: (1) `catch (QueryException $e)` menjadi
`catch (QueryException)` karena variabel tidak dipakai; (2) literal status menjadi konstanta
`Activity::STATUS_*`; (3) `ensureCompleteForPublish` dipecah menjadi `missingPublishFields`.

## 7. README
Tambahkan ke `README.md` repository bagian singkat: cara menjalankan (`composer install`, `.env`,
`php artisan migrate --seed`, `php artisan storage:link`), daftar fitur Task 1-3 + Independent + Add-on,
dan tabel checkpoint tag (modul3-crud-baseline, modul3-special-task1, -task2, -task3, -independent, -final).

## 8. Bersihkan sebelum commit akhir
Pastikan tidak ada kode eksperimen (route sementara, `DB::enableQueryLog()`, `dd()`) tersisa.
```bash
git add .
git commit -m "feat(addon): poster storage dan refaktorisasi keterbacaan"
git tag modul3-special-final
git push origin modul3-special-challenge --tags
```
