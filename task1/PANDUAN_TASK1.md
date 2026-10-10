# Task 1 - Relational CRUD dan Integrity

Checkpoint commit: `modul3-special-task1`

> Catatan: nama file/kelas di bawah memakai konvensi Laravel standar (ActivityController,
> StoreActivityRequest, UpdateActivityRequest, view `activities/*`). Kalau namamu beda,
> sesuaikan saja isinya.

## 0. Persiapan
```bash
git checkout -b modul3-special-challenge
```

## 1. Salin file baru (struktur folder sama dengan di zip)
- `database/migrations/2026_10_03_000001_create_categories_table.php`
- `database/migrations/2026_10_03_000002_add_category_id_to_activities_table.php`
- `database/migrations/2026_10_03_000003_add_unique_code_to_activities_table.php`
- `database/seeders/CategorySeeder.php`
- `app/Models/Category.php`
- `app/Http/Controllers/CategoryController.php`
- `resources/views/categories/index.blade.php`

## 2. Ubah file yang sudah ada

### a. `app/Models/Activity.php`
Tambahkan `category_id` ke `$fillable` dan relationship `category`:
```php
use Illuminate\Database\Eloquent\Relations\BelongsTo;

protected $fillable = [
    'category_id',   // <-- tambahkan
    // ... field lain yang sudah ada
];

public function category(): BelongsTo
{
    return $this->belongsTo(Category::class);
}
```

### b. `database/seeders/DatabaseSeeder.php`
```php
public function run(): void
{
    $this->call(CategorySeeder::class);
    // seeder lain (kalau ada)
}
```

### c. `routes/web.php`
```php
use App\Http\Controllers\CategoryController;

Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');
```

### d. Form Request create (`StoreActivityRequest`) dan update (`UpdateActivityRequest`)
Pada `rules()` tambahkan/ubah dua rule ini (sisanya biarkan seperti baseline):
```php
use Illuminate\Validation\Rule;

// StoreActivityRequest
'category_id' => ['required', 'exists:categories,id'],
'code'        => ['required', 'string', 'max:30', 'unique:activities,code'],

// UpdateActivityRequest  (abaikan record yang sedang diedit)
'category_id' => ['required', 'exists:categories,id'],
'code'        => ['required', 'string', 'max:30',
                  Rule::unique('activities', 'code')->ignore($this->route('activity'))],
```
Kalau kamu memakai satu Form Request untuk create dan update, pakai versi update
(`ignore($this->route('activity'))` aman untuk create karena parameter route-nya null).

Opsional, supaya pesan error lebih ramah:
```php
public function messages(): array
{
    return [
        'category_id.required' => 'Kategori wajib dipilih.',
        'category_id.exists'   => 'Kategori yang dipilih tidak tersedia.',
        'code.unique'          => 'Kode kegiatan sudah dipakai.',
    ];
}
```

### e. `app/Http/Controllers/ActivityController.php`
Kirim daftar kategori ke form create dan edit:
```php
use App\Models\Category;

public function create()
{
    return view('activities.create', [
        'categories' => Category::ordered()->get(),
    ]);
}

public function edit(Activity $activity)
{
    return view('activities.edit', [
        'activity'   => $activity,
        'categories' => Category::ordered()->get(),
    ]);
}
```
`store()` dan `update()` tidak perlu diubah selama memakai `$request->validated()`.
Pada `index()` JANGAN dulu tambah `with('category')`: lazy loading dibiarkan sengaja
agar N+1 bisa dibuktikan di Task 3 (Eksperimen 5).

### f. View form create & edit (`resources/views/activities/create.blade.php`, `edit.blade.php`)
Tambahkan dropdown kategori di dalam `<form>` (di edit, pilih nilai lama):
```blade
<div>
    <label for="category_id">Kategori</label>
    <select name="category_id" id="category_id" required>
        <option value="">-- Pilih kategori --</option>
        @foreach ($categories as $category)
            <option value="{{ $category->id }}"
                @selected(old('category_id', $activity->category_id ?? null) == $category->id)>
                {{ $category->name }}
            </option>
        @endforeach
    </select>
    @error('category_id') <small style="color:red">{{ $message }}</small> @enderror
</div>
```
(Di `create.blade.php` variabel `$activity` tidak ada; `?? null` sudah menangani itu.)

### g. View index & show
Tampilkan nama kategori lewat relationship:
```blade
{{-- index.blade.php, di dalam loop @foreach ($activities as $activity) --}}
<td>{{ $activity->category->name }}</td>

{{-- show.blade.php --}}
<p>Kategori: {{ $activity->category->name }}</p>
```
Tambahkan juga header kolom "Kategori" di tabel index.

## 3. Jalankan
Database latihan boleh di-reset (sesuai modul):
```bash
php artisan migrate:fresh --seed
```
Kalau tabel `activities` sudah berisi data dan TIDAK ingin di-reset:
```bash
php artisan migrate
php artisan db:seed --class=CategorySeeder
```
(migration kedua otomatis mengisi baris lama dengan kategori "Lainnya".)

Siapkan data uji: minimal 2 kategori (sudah dari seeder) dan 3 kegiatan lewat form create.

## 4. Pengujian enam skenario (ambil screenshot untuk kolom Bukti)

| # | Skenario | Cara uji | Yang di-screenshot |
|---|----------|----------|--------------------|
| 1 | Create kategori valid | Isi form create, pilih kategori | Halaman index/show menampilkan nama kategori |
| 2 | Create category_id tidak valid | Buka DevTools, ubah value `<option>` jadi `9999`, submit | Pesan error "Kategori yang dipilih tidak tersedia" |
| 3 | Create kode duplikat | Buat kegiatan dengan kode yang sudah ada | Pesan error kode duplikat |
| 4 | Update tanpa ganti kode | Edit kegiatan, ubah judul saja, simpan | Berhasil tersimpan tanpa error duplikat |
| 5 | Hapus kategori yang dipakai | Buka `/categories`, klik Hapus pada kategori yang punya kegiatan | Pesan "tidak dapat dihapus", jumlah kegiatan tetap |
| 6 | Hapus kategori kosong | Hapus kategori "Lainnya" (kalau belum dipakai) | Pesan sukses, kategori hilang dari daftar |

Bukti constraint di level database (untuk worksheet bagian B, jalankan di `php artisan tinker`):
```php
// Kegiatan dengan category_id yatim -> DITOLAK DATABASE (foreign key), bukan aplikasi
DB::table('activities')->insert([
    'category_id' => 9999, 'code' => 'TES-FK', 'title' => 'Uji FK',
    'start_at' => now(), 'end_at' => now()->addDay(),
    'location' => 'Lab', 'capacity' => 10, 'status' => 'draft',
    'created_at' => now(), 'updated_at' => now(),
]);

// Hapus kategori yang dipakai -> DITOLAK DATABASE (restrictOnDelete)
App\Models\Category::first()->delete();   // gunakan kategori yang punya kegiatan
```
Screenshot error `QueryException` (SQLSTATE 23000 / foreign key constraint fails)
cocok untuk kolom Bukti di tabel relasi. Kalau salah satu field wajib di tabel
`activities`-mu berbeda dari contoh di atas, sesuaikan isi array insert-nya.

Bukti relationship dua arah (Eksperimen 1 langkah 4):
```php
App\Models\Activity::first()->category->name;
App\Models\Category::first()->activities()->count();
```

Bukti struktur tabel (foreign key + unique), pilih salah satu sesuai DB-mu:
```bash
php artisan db:table activities
```

## 5. Commit
```bash
git add .
git commit -m "feat(task1): relasi category-activity, foreign key restrict, unique code, delete policy kategori"
git tag modul3-special-task1
```
Isi hash commit ke worksheet bagian A (baris Task 1).
