@extends('layouts.app')

@section('title', 'Detail Pesanan')

@section('content')
<h1 class="page-heading">Detail pesanan</h1>
<p class="muted">Nomor #{{ $order->id_order }} · {{ $order->tanggal_order->format('d/m/Y H:i') }}</p>

<section class="panel" style="margin-top: 22px">
    @foreach ($order->details as $detail)
        <div class="order-line">
            <span>
                <strong>{{ $detail->product->nama_barang }}</strong><br>
                <span class="muted">Rp {{ number_format((float) $detail->harga_satuan, 0, ',', '.') }} × {{ $detail->jumlah_beli }}</span>
            </span>
            <strong>Rp {{ number_format((float) $detail->harga_satuan * $detail->jumlah_beli, 0, ',', '.') }}</strong>
        </div>
    @endforeach
    <div class="cart-total">
        <span>Total bayar</span>
        <span>Rp {{ number_format((float) $order->total_harga, 0, ',', '.') }}</span>
    </div>
    <p><strong>Alamat pengiriman:</strong><br>{{ $order->alamat_pengiriman }}</p>
    <a class="button" href="{{ route('orders.index') }}">Kembali ke riwayat</a>
</section>
@endsection
