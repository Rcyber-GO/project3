@extends('layouts.app')

@section('title', 'Daftar Produk')

@section('content')
<style>
    .catalog-products {
        margin-top: 24px;
        border-top: 1px solid #e6eaf0;
    }
    .catalog-product {
        display: flex;
        align-items: center;
        gap: 16px;
        padding: 16px 0;
        border-bottom: 1px solid #e6eaf0;
    }
    .catalog-product-image {
        width: 72px;
        height: 72px;
        flex: 0 0 72px;
        object-fit: cover;
        border-radius: 8px;
        background: #eaf1ff;
    }
    .catalog-product-info {
        flex: 1;
        min-width: 0;
    }
    .catalog-product-info h2 {
        margin: 0 0 4px;
        font-size: 1rem;
    }
    .catalog-product-info p {
        margin: 0 0 6px;
        color: #667085;
        font-size: .9rem;
    }
    .catalog-product-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
    }
    .catalog-product-price {
        font-weight: 700;
    }
    .catalog-product-action {
        flex: 0 0 auto;
    }
    @media (max-width: 560px) {
        .catalog-product {
            align-items: flex-start;
            flex-wrap: wrap;
        }
        .catalog-product-info {
            flex-basis: calc(100% - 88px);
        }
        .catalog-product-action {
            margin-left: 88px;
        }
    }
</style>
<section>
    <h1 class="page-heading">Perlengkapan pilihan untuk aktivitas Anda</h1>
    <p class="muted">Pilih kebutuhan Anda dari katalog toko kami.</p>
    @guest
        <p class="muted">Anda dapat melihat produk tanpa login. <a href="{{ route('login') }}">Masuk</a> untuk membeli.</p>
    @endguest

    <div class="catalog-products">
        @foreach ($products as $product)
            <article class="catalog-product">
                <img class="catalog-product-image" src="{{ asset('images/'.$product->gambar) }}" alt="{{ $product->nama_barang }}">
                <div class="catalog-product-info">
                    <h2>{{ $product->nama_barang }}</h2>
                    <p class="product-description">{{ $product->deskripsi }}</p>
                    <div class="catalog-product-meta">
                        <span class="catalog-product-price">Rp {{ number_format((float) $product->harga, 0, ',', '.') }}</span>
                        <span class="stock">Stok: {{ $product->stok }}</span>
                    </div>
                </div>
                <div class="catalog-product-action">
                    @auth
                        <form method="POST" action="{{ route('cart.add', $product) }}">
                            @csrf
                            <button class="button" type="submit" @disabled($product->stok === 0)>
                                {{ $product->stok === 0 ? 'Stok habis' : 'Tambah ke keranjang' }}
                            </button>
                        </form>
                    @else
                        <a class="button" href="{{ route('login') }}">Masuk untuk membeli</a>
                    @endauth
                </div>
            </article>
        @endforeach
    </div>
</section>
@endsection
