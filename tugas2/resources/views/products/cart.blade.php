<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keranjang Belanja</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f5f5f5;
            margin: 0;
            color: #111827;
        }

        .container {
            max-width: 900px;
            margin: 40px auto;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.08);
            padding: 24px 28px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 16px;
            margin-bottom: 20px;
        }

        h1 {
            margin: 0;
            font-size: 2rem;
        }

        .plain {
            color: #6b7280;
            font-weight: 600;
        }

        .item {
            border-bottom: 1px solid #e5e7eb;
            padding: 18px 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 18px;
        }

        .name {
            font-size: 1.1rem;
            font-weight: 700;
            margin-bottom: 6px;
        }

        .controls {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .small-button {
            border: 1px solid #d1d5db;
            background: white;
            border-radius: 6px;
            width: 30px;
            height: 30px;
            cursor: pointer;
            font-size: 1rem;
            font-weight: 700;
        }

        .qty {
            min-width: 26px;
            text-align: center;
            font-weight: 700;
        }

        .delete {
            background: #ef4444;
            color: white;
            border: 0;
            border-radius: 6px;
            padding: 8px 10px;
            cursor: pointer;
            font-weight: 600;
        }

        .total {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 16px;
            padding-top: 18px;
            border-top: 1px solid #e5e7eb;
            font-size: 1.05rem;
        }

        .total strong {
            font-size: 1.2rem;
        }

        .actions {
            margin-top: 22px;
            display: flex;
            justify-content: flex-end;
        }

        .clear-button {
            background: #111827;
            color: white;
            border: 0;
            border-radius: 8px;
            padding: 10px 16px;
            cursor: pointer;
            font-weight: 700;
        }

        .empty {
            text-align: center;
            padding: 24px 0 16px;
            color: #4b5563;
        }

        .link-btn {
            display: inline-block;
            margin-top: 10px;
            background: #2563eb;
            color: white;
            text-decoration: none;
            padding: 10px 16px;
            border-radius: 8px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Keranjang belanja</h1>
            <div class="plain">Tanpa login</div>
        </div>

        @if (empty($items))
            <div class="empty">
                <p>Keranjang masih kosong.</p>
                <a class="link-btn" href="{{ route('products.index') }}">Lanjut belanja</a>
            </div>
        @else
            @foreach($items as $item)
                <div class="item">
                    <div>
                        <div class="name">{{ $item['product']->name }}</div>
                        <div>Rp {{ number_format($item['product']->price, 0, ',', '.') }} x {{ $item['qty'] }} = Rp {{ number_format($item['subtotal'], 0, ',', '.') }}</div>
                    </div>

                    <div class="controls">
                        <form method="POST" action="{{ route('cart.update', $item['product']->id) }}">
                            @csrf
                            <input type="hidden" name="change" value="-1">
                            <button type="submit" class="small-button">-</button>
                        </form>
                        <span class="qty">{{ $item['qty'] }}</span>
                        <form method="POST" action="{{ route('cart.update', $item['product']->id) }}">
                            @csrf
                            <input type="hidden" name="change" value="1">
                            <button type="submit" class="small-button">+</button>
                        </form>
                        <form method="POST" action="{{ route('cart.remove', $item['product']->id) }}">
                            @csrf
                            <button type="submit" class="delete">hps</button>
                        </form>
                    </div>
                </div>
            @endforeach

            <div class="total">
                <span>Total</span>
                <strong>Rp {{ number_format($total, 0, ',', '.') }}</strong>
            </div>

            <div class="actions">
                <form method="POST" action="{{ route('cart.clear') }}">
                    @csrf
                    <button type="submit" class="clear-button">Kosongkan keranjang</button>
                </form>
            </div>
        @endif
    </div>
</body>
</html>
