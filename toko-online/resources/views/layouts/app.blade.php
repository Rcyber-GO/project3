<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Toko Pintar') | Toko Pintar</title>
    <style>
        :root {
            color-scheme: light;
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            color: #172033;
            background: #f5f7fb;
        }
        * { box-sizing: border-box; }
        body { margin: 0; min-width: 320px; }
        a { color: inherit; }
        .site-header { background: #fff; border-bottom: 1px solid #e6eaf0; }
        .nav { max-width: 1120px; margin: auto; padding: 18px 24px; display: flex; gap: 22px; align-items: center; }
        .brand { color: #1546a0; font-size: 1.25rem; font-weight: 800; text-decoration: none; margin-right: auto; }
        .nav-link { color: #46536a; text-decoration: none; font-weight: 650; }
        .nav form { margin: 0; }
        .nav button, .button {
            display: inline-block; border: 0; border-radius: 9px; background: #245bd6; color: #fff;
            padding: 10px 15px; text-decoration: none; font: inherit; font-weight: 700; cursor: pointer;
        }
        .nav .logout-button { background: transparent; color: #46536a; padding: 8px 0; }
        .page { max-width: 1120px; margin: 34px auto; padding: 0 24px 48px; }
        .page-heading { margin: 0 0 8px; font-size: clamp(1.7rem, 4vw, 2.3rem); }
        .muted { color: #667085; }
        .panel { background: white; border: 1px solid #e6eaf0; border-radius: 16px; padding: 24px; box-shadow: 0 8px 24px #192a4410; }
        .alert { border-radius: 10px; padding: 12px 15px; margin: 0 0 18px; }
        .alert-success { background: #eaf8ef; color: #17653a; }
        .alert-error { background: #fff0ee; color: #a1281e; }
        .alert ul { margin: 0; padding-left: 20px; }
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(230px, 1fr)); gap: 18px; margin-top: 24px; }
        .product-card { background: #fff; border: 1px solid #e6eaf0; border-radius: 14px; overflow: hidden; box-shadow: 0 5px 18px #192a440b; }
        .product-image { display: block; width: 100%; height: 175px; object-fit: cover; background: #eaf1ff; }
        .product-content { padding: 17px; }
        .product-content h2 { margin: 0 0 6px; font-size: 1.12rem; }
        .product-description { color: #667085; min-height: 42px; margin: 0 0 12px; font-size: .92rem; line-height: 1.45; }
        .product-bottom { display: flex; justify-content: space-between; align-items: center; gap: 12px; margin-top: 15px; }
        .price { font-weight: 800; font-size: 1.05rem; }
        .stock { color: #667085; font-size: .9rem; }
        .button[disabled] { background: #aab3c2; cursor: not-allowed; }
        .table-like { display: grid; gap: 0; }
        .cart-row { display: grid; grid-template-columns: minmax(180px, 2fr) 1fr 1fr auto; align-items: center; gap: 18px; padding: 18px 0; border-bottom: 1px solid #e6eaf0; }
        .cart-product { display: flex; align-items: center; gap: 14px; }
        .cart-product img { width: 72px; height: 62px; border-radius: 9px; object-fit: cover; }
        .cart-product strong { display: block; margin-bottom: 4px; }
        .quantity-form, .remove-form { display: inline-flex; margin: 0; }
        .quantity-button { width: 34px; height: 34px; border: 1px solid #d8dfeb; border-radius: 8px; background: #fff; font-size: 1.15rem; cursor: pointer; }
        .quantity-value { display: inline-block; min-width: 38px; text-align: center; font-weight: 750; line-height: 34px; }
        .remove-button { border: 0; background: transparent; color: #b42318; font: inherit; font-weight: 700; cursor: pointer; }
        .cart-total { display: flex; justify-content: space-between; gap: 18px; padding: 22px 0; font-size: 1.2rem; font-weight: 800; }
        .checkout-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; align-items: start; }
        label { display: block; font-weight: 700; margin-bottom: 7px; }
        input, textarea { width: 100%; padding: 11px 12px; border: 1px solid #cfd6e2; border-radius: 9px; font: inherit; }
        textarea { min-height: 100px; resize: vertical; }
        .form-field { margin-bottom: 16px; }
        .auth-panel { max-width: 500px; margin: 40px auto; }
        .auth-panel .button { width: 100%; }
        .order-card { margin-top: 16px; }
        .order-line { display: flex; justify-content: space-between; gap: 16px; border-bottom: 1px solid #e6eaf0; padding: 12px 0; }
        @media (max-width: 680px) {
            .nav { gap: 12px; padding: 14px 16px; flex-wrap: wrap; }
            .page { padding: 0 16px 36px; margin-top: 24px; }
            .cart-row { grid-template-columns: 1fr auto; gap: 10px; }
            .cart-product { grid-column: 1 / -1; }
            .checkout-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
<header class="site-header">
    <nav class="nav">
        <a class="brand" href="{{ route('products.index') }}">Toko Pintar</a>
        <a class="nav-link" href="{{ route('products.index') }}">Produk</a>
        @auth
            <a class="nav-link" href="{{ route('cart.index') }}">Keranjang</a>
            <a class="nav-link" href="{{ route('orders.index') }}">Pesanan</a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="logout-button" type="submit">Keluar</button>
            </form>
        @else
            <a class="nav-link" href="{{ route('login') }}">Masuk</a>
            <a class="button" href="{{ route('register') }}">Daftar</a>
        @endauth
    </nav>
</header>
<main class="page">
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-error">{{ session('error') }}</div>
    @endif
    @if ($errors->any())
        <div class="alert alert-error">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    @yield('content')
</main>
</body>
</html>
