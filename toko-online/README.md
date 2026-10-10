# Toko Pintar — Tugas Program Toko Online

Aplikasi toko online Laravel 13 dengan katalog publik, akun pengguna, keranjang khusus pengguna login, checkout, pengelolaan stok, dan riwayat pesanan.

## Fitur

- Pengunjung dapat melihat katalog 10 produk tanpa login.
- Login dengan username dan password; password disimpan dalam bentuk hash.
- Pengunjung dapat membuat akun dari halaman **Daftar**.
- Hanya pengguna login yang dapat menambah atau mengubah isi keranjang dan checkout.
- Jumlah keranjang divalidasi terhadap stok yang tersedia.
- Checkout membaca harga dan stok dari database, membuat pesanan beserta detailnya, mengurangi stok dalam transaksi database, lalu mengosongkan keranjang.
- Riwayat dan detail pesanan hanya dapat dibuka pemilik pesanan.
- Ongkos kirim tidak ditambahkan ke total.

## Menjalankan aplikasi

Project ini dikonfigurasi memakai SQLite supaya dapat langsung dijalankan di lingkungan pengembangan ini.

```bash
cd toko-online
php artisan migrate --seed
php artisan serve
```

Buka `http://127.0.0.1:8000`.

### Akun demo

- Username: `budi`
- Password: `password123`

Pengguna juga dapat membuat akun sendiri lewat menu **Daftar**.

### Menggunakan MySQL

Buat database `toko_online`, lalu atur bagian database pada `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=toko_online
DB_USERNAME=root
DB_PASSWORD=
```

Lalu jalankan:

```bash
php artisan migrate --seed
```

## Struktur data utama

- `users`: id pengguna, nama lengkap, email, username, hash password, nomor telepon, dan alamat.
- `products`: 10 produk dengan gambar, harga, dan stok.
- `orders`: pemilik, waktu checkout, total harga, dan alamat pengiriman.
- `order_details`: harga satuan pada saat pembelian dan jumlah produk.

Keranjang menyimpan hanya ID barang dan jumlah di session. Nama, harga, serta stok selalu diambil ulang dari database; nilai harga dari browser tidak digunakan sebagai sumber checkout.

## Validasi

Feature tests dapat dijalankan dengan:

```bash
php artisan test --filter=OnlineStoreTest
```
