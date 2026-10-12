# Modul 4 Bagian 2 — Stateless Shopping Cart

Keranjang belanja Laravel yang **tidak menyimpan keranjang di session maupun cookie**. Keranjang disimpan di browser (variabel JavaScript `cart`) dan dikirim utuh di setiap request. Laravel menghitung item, subtotal, dan total menggunakan harga dari katalog di server.

| ID | Produk | Harga |
|---|---|---|
| 1 | Buku Tulis | Rp5.000 |
| 2 | Pulpen | Rp3.000 |
| 3 | Penggaris | Rp4.000 |

## File utama

- `app/Http/Controllers/CartController.php`: katalog, `add`, `update`, dan `calculate`
- `routes/web.php`: `GET /`, `POST /cart/add`, `POST /cart/update`
- `resources/views/shop.blade.php`: tampilan toko dan `fetch()` JSON dengan `X-CSRF-TOKEN`
- `tests/Feature/StatelessCartTest.php`: pengujian otomatis
- `postman/Stateless-Shopping-Cart.postman_collection.json`: koleksi Postman (token CSRF diambil otomatis)

## Menjalankan

```bash
cd stateless-shop
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan serve
```

Buka `http://127.0.0.1:8000`. Database hanya dipakai untuk tabel session bawaan Laravel (token CSRF), bukan untuk keranjang.

## Pengujian

```bash
php artisan test
npx newman run postman/Stateless-Shopping-Cart.postman_collection.json
```
