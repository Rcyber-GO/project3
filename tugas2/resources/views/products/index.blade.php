<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Toko Alat Tulis</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f5f5f5;
            margin: 0;
            color: #1f2937;
        }

        .container {
            max-width: 960px;
            margin: 40px auto;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.08);
            padding: 24px 28px;
        }

        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 16px;
            margin-bottom: 20px;
        }

        .topbar h1 {
            margin: 0;
            font-size: 2rem;
        }

        .cart-link {
            text-decoration: none;
            color: #111827;
            font-weight: 700;
            background: #f3f4f6;
            padding: 10px 16px;
            border-radius: 999px;
        }

        .products {
            display: grid;
            gap: 18px;
        }

        .product-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 16px;
        }

        .product-main {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .thumb {
            width: 72px;
            height: 72px;
            background: linear-gradient(135deg, #dbeafe, #bfdbfe);
            border-radius: 12px;
            display: flex;
            justify-content: center;
            align-items: center;
            font-weight: 700;
            color: #1d4ed8;
            font-size: 1.2rem;
        }

        .name {
            font-size: 1.1rem;
            font-weight: 700;
            margin: 0;
        }

        .meta {
            color: #4b5563;
            margin-top: 4px;
            font-size: 0.95rem;
        }

        .price {
            font-weight: 700;
            color: #111827;
            margin-top: 4px;
        }

        .button {
            background: #2563eb;
            color: white;
            border: 0;
            border-radius: 8px;
            padding: 10px 16px;
            cursor: pointer;
            font-weight: 600;
        }

        .alert {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #991b1b;
            padding: 12px 14px;
            border-radius: 8px;
            margin-bottom: 18px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="topbar">
            <h1>Toko Alat Tulis</h1>
            <a class="cart-link" href="{{ route('cart.index') }}">Keranjang ({{ $cartCount }})</a>
        </div>

        @if (session('error'))
            <div class="alert">{{ session('error') }}</div>
        @endif

        <div>
            <h2>Daftar barang</h2>
        </div>

        <div class="products">
            @foreach($products as $product)
                <div class="product-item">
                    <div class="product-main">
                        <div class="thumb">{{ strtoupper(substr($product->name, 0, 2)) }}</div>
                        <div>
                            <p class="name">{{ $product->name }}</p>
                            <div class="meta">Stok: {{ $product->stock }}</div>
                            <div class="price">Rp {{ number_format($product->price, 0, ',', '.') }}</div>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('cart.add', $product->id) }}">
                        @csrf
                        <button type="submit" class="button">Masukkan ke keranjang</button>
                    </form>
                </div>
            @endforeach
        </div>
    </div>
</body>
</html>
