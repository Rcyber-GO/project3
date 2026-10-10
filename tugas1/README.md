# Tugas 1: Login dengan Password Terenkripsi (Laravel 13)

Proyek ini dibuat untuk memenuhi tugas login aman dengan Laravel. Aplikasi memiliki:
- halaman login
- validasi username dan password
- hash password saat disimpan
- proteksi halaman dashboard via middleware `auth`
- logout menggunakan metode `POST` dengan `@csrf`

## Catatan database
Pada lingkungan ini, aplikasi berjalan dengan SQLite agar dapat langsung dijalankan di mesin lokal. Jika ingin mengikuti ketentuan tugas dengan MySQL, gunakan konfigurasi berikut di `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=db_tugas
DB_USERNAME=root
DB_PASSWORD=
```

## Jawaban pertanyaan
### a. Mengapa password harus di-hash? Lihat kolom password di tabel users dan jelaskan isinya.
Password harus di-hash karena password asli tidak boleh disimpan dalam bentuk teks biasa. Jika password disimpan dalam bentuk plain text, data pengguna akan sangat mudah dicuri atau dibaca oleh pihak yang tidak berwenang. Pada tabel `users`, kolom `password` berisi nilai seperti `$2y$10$...` yang merupakan string hash hasil algoritma bcrypt/argon2. Hash bukan enkripsi yang bisa dibalik: saat login, Laravel membandingkan password yang dimasukkan dengan hash tanpa membaca password asli dari database.

### b. Mengapa logout memakai metode POST dengan @csrf, bukan link biasa?
Logout menggunakan metode `POST` karena tindakan logout adalah perubahan state sesi dan bersifat merusak/menutup sesi aktif. Dengan `@csrf`, aplikasi memastikan permintaan logout berasal dari form yang sah dan bukan request yang dibuat dari sumber lain (CSRF attack). Link biasa menggunakan `GET` bisa merusak keamanan karena URL bisa diakses secara tidak sengaja atau dipicu oleh otomatisasi.

### c. Apa fungsi middleware auth dan guest? Apa yang terjadi jika /dashboard dibuka tanpa login?
- `auth`: memastikan pengguna sudah login. Jika belum login, Laravel akan otomatis mengarahkan ke halaman login.
- `guest`: memastikan pengguna belum login. Jika sudah login, user akan diarahkan ke halaman dashboard.
Jika `/dashboard` dibuka tanpa login, middleware `auth` akan memblokir akses dan melakukan redirect ke `/login`.

### d. Apa fungsi session()->regenerate() setelah login berhasil?
`session()->regenerate()` membuat identitas sesi baru agar session lama tidak dipakai lagi. Fungsinya untuk mencegah session fixation, yaitu serangan di mana penyerang memaksa pengguna memakai sesi yang sudah diketahui sebelumnya. Setelah login berhasil, sesi baru dibuat agar aman dan tidak bisa dipakai ulang oleh pihak yang tidak berwenang.

## Cara menjalankan
```bash
composer install
php artisan migrate --seed
php artisan serve
```

Kemudian buka:
- `http://localhost:8000/login`

Akun seeder:

| Username | Password |
|---|---|
| `budi` | `budi123` |
| `siti` | `siti123` |
