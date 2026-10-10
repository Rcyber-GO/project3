@extends('layouts.app')

@section('title', 'Riwayat Pesanan')

@section('content')
<h1 class="page-heading">Riwayat pesanan</h1>
<p class="muted">Pesanan yang ditampilkan hanya milik akun Anda.</p>

@forelse ($orders as $order)
    <article class="panel order-card">
        <div class="order-line">
            <div>
                <strong>Pesanan #{{ $order->id_order }}</strong>
                <div class="muted">{{ $order->tanggal_order->format('d/m/Y H:i') }}</div>
            </div>
            <strong>Rp {{ number_format((float) $order->total_harga, 0, ',', '.') }}</strong>
        </div>
        <p class="muted">{{ $order->details->count() }} jenis produk</p>
        <a class="button" href="{{ route('orders.show', $order) }}">Lihat detail</a>
    </article>
@empty
    <section class="panel" style="margin-top: 22px">
        <p>Belum ada pesanan.</p>
        <a class="button" href="{{ route('products.index') }}">Mulai belanja</a>
    </section>
@endforelse
@endsection
