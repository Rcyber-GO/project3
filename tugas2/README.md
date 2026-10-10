# Tugas 2: Keranjang Belanja Tanpa Login

Aplikasi ini dibuat dengan Laravel dan menggunakan mekanisme `session` untuk menyimpan data keranjang belanja. Data keranjang disimpan dalam sesi server, bukan di cookies atau Local Storage, sehingga lebih aman dan lebih mudah dikontrol oleh server.

Daftar barang disimpan di tabel `barang`. Keranjang disimpan sebagai pasangan `id_barang => jumlah`. Nama dan harga tidak disimpan dalam session; keduanya selalu diambil dari tabel `barang`.

## Mekanisme yang dipilih
Session

Alasan:
- cocok untuk keranjang tanpa login karena data tetap ada saat pengguna membuka halaman lain atau refresh.
- session menyimpan data keranjang di server; browser hanya mengirim cookie ID session, bukan isi keranjangnya.
- dibanding Local Storage, server dapat memvalidasi perubahan keranjang dan menghitung harga tanpa mempercayai nilai dari browser.

## Kelebihan dan kekurangan
- Kelebihan session dibanding cookies: isi keranjang tersimpan di server sehingga tidak bisa diedit langsung sebagai nilai cookie.
- Kelebihan session dibanding Local Storage: perubahan jumlah dapat diperiksa server terhadap stok produk.
- Kekurangan session dibanding cookies: keranjang tidak otomatis muncul di browser lain karena ID session berbeda.
- Kekurangan session dibanding Local Storage: data tidak bisa dipindah antar browser/device dengan cara yang sama.

## Pertanyaan tugas
1. Mekanisme yang dipilih: session. Pilihan ini tepat karena aplikasi tidak memerlukan login dan keranjang dapat bertahan setelah refresh menggunakan sesi server.
2. Kelebihan: lebih aman. Kekurangan: tidak bertahan di browser lain/mesin lain seperti Local Storage.
3. Isi keranjang tidak dapat diubah langsung dari DevTools karena berada di server. Browser hanya menyimpan cookie ID session. Perubahan jumlah dilakukan melalui tombol `+`/`-`; server hanya menerima perubahan satu unit dan memeriksa jumlahnya terhadap stok. Mengubah atau mencuri cookie session adalah risiko berbeda dan tidak mengungkap isi keranjang.
4. Harga tidak boleh dipercaya dari data yang dikirim atau disimpan di browser karena user dapat memodifikasi nilai. Saat checkout, server harus selalu membaca harga asli dari database berdasarkan ID produk, lalu menghitung total dari data produk yang valid di server.
5. Jika pengguna membuka situs dari browser lain, keranjang tidak muncul karena browser itu memiliki cookie ID session yang berbeda. Di aplikasi ini data session disimpan di database, berlaku 120 menit sejak aktivitas terakhir secara default, dan tidak langsung hilang saat browser ditutup.

## Cara menjalankan
```bash
cd tugas2
php artisan migrate --seed
php artisan serve
```

Buka:
- `http://localhost:8000/` untuk daftar produk
- `http://localhost:8000/keranjang` untuk keranjang

## Catatan
Untuk kebutuhan lokal, project ini menggunakan SQLite agar dapat dijalankan langsung di mesin pengembang tanpa MySQL. Jika ingin memakai MySQL, sesuaikan `.env` dengan konfigurasi database yang sesuai.
