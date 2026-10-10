@extends('layouts.app')

@section('title', 'Keranjang')

@section('content')
<h1 class="page-heading">Keranjang belanja</h1>
<p class="muted">Periksa jumlah dan total sebelum checkout.</p>

@if (count($items) === 0)
    <section class="panel" style="margin-top: 22px">
        <p>Keranjang Anda masih kosong.</p>
        <a class="button" href="{{ route('products.index') }}">Lihat produk</a>
    </section>
@else
    <div class="checkout-grid" style="margin-top: 22px">
        <section class="panel">
            <div class="table-like">
                @foreach ($items as $item)
                    <div class="cart-row">
                        <div class="cart-product">
                            <img src="{{ asset('images/'.$item['product']->gambar) }}" alt="{{ $item['product']->nama_barang }}">
                            <div>
                                <strong>{{ $item['product']->nama_barang }}</strong>
                                <span class="muted">Rp {{ number_format((float) $item['product']->harga, 0, ',', '.') }} / item</span>
                            </div>
                        </div>
                        <div>
                            <form class="quantity-form" method="POST" action="{{ route('cart.update', $item['product']) }}">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="quantity" value="{{ $item['quantity'] - 1 }}">
                                <button class="quantity-button" type="submit" aria-label="Kurangi jumlah" @disabled($item['quantity'] <= 1)>−</button>
                            </form>
                            <span class="quantity-value">{{ $item['quantity'] }}</span>
                            <form class="quantity-form" method="POST" action="{{ route('cart.update', $item['product']) }}">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="quantity" value="{{ $item['quantity'] + 1 }}">
                                <button class="quantity-button" type="submit" aria-label="Tambah jumlah" @disabled($item['quantity'] >= $item['product']->stok)>+</button>
                            </form>
                        </div>
                        <strong>Rp {{ number_format($item['subtotal'] / 100, 0, ',', '.') }}</strong>
                        <form class="remove-form" method="POST" action="{{ route('cart.remove', $item['product']) }}">
                            @csrf
                            @method('DELETE')
                            <button class="remove-button" type="submit">Hapus</button>
                        </form>
                    </div>
                @endforeach
                <div class="cart-total">
                    <span>Total</span>
                    <span>Rp {{ number_format($total / 100, 0, ',', '.') }}</span>
                </div>
            </div>
        </section>

        <section class="panel">
            <h2 style="margin-top: 0">Checkout</h2>
            <p class="muted">Total bayar sama dengan total harga barang. Ongkos kirim tidak dihitung.</p>
            <form method="POST" action="{{ route('checkout') }}">
                @csrf
                <div class="form-field">
                    <label for="alamat_pengiriman">Alamat pengiriman</label>
                    <textarea id="alamat_pengiriman" name="alamat_pengiriman" required>{{ old('alamat_pengiriman', $address) }}</textarea>
                </div>
                <button class="button" type="submit">Buat pesanan</button>
            </form>
        </section>
    </div>
@endif
@endsection
