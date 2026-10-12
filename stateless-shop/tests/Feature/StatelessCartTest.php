<?php

namespace Tests\Feature;

use Tests\TestCase;

class StatelessCartTest extends TestCase
{
    public function test_halaman_toko_tampil_dengan_token_csrf(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('Daftar Produk')
            ->assertSee('name="csrf-token"', false);
    }

    public function test_skenario_modul_dua_buku_tulis_satu_pulpen_total_13000(): void
    {
        $cart = [];

        foreach ([1, 1, 2] as $productId) {
            $data = $this->postJson('/cart/add', [
                'cart' => $cart,
                'product_id' => $productId,
            ])->assertOk()->json();

            $cart = collect($data['items'])->pluck('quantity', 'id')->all();
        }

        $this->assertSame([1 => 2, 2 => 1], $cart);
        $this->assertSame(13000, $data['total']);
    }

    public function test_payload_contoh_modul_menghasilkan_total_13000(): void
    {
        $this->postJson('/cart/add', [
            'cart' => ['1' => 2],
            'product_id' => 2,
        ])->assertOk()->assertExactJson([
            'items' => [
                ['id' => 1, 'name' => 'Buku Tulis', 'price' => 5000, 'quantity' => 2, 'subtotal' => 10000],
                ['id' => 2, 'name' => 'Pulpen', 'price' => 3000, 'quantity' => 1, 'subtotal' => 3000],
            ],
            'total' => 13000,
        ]);
    }

    public function test_payload_sama_selalu_menghasilkan_respons_sama(): void
    {
        $payload = ['cart' => ['1' => 2], 'product_id' => 2];

        $first = $this->postJson('/cart/add', $payload)->json();
        $second = $this->postJson('/cart/add', $payload)->json();
        $third = $this->postJson('/cart/add', $payload)->json();

        $this->assertSame($first, $second);
        $this->assertSame($second, $third);
    }

    public function test_server_tidak_menyimpan_keranjang_ke_session(): void
    {
        $this->postJson('/cart/add', ['cart' => [], 'product_id' => 1])
            ->assertOk()
            ->assertSessionMissing('cart');

        // Request baru dengan keranjang kosong tidak "mengingat" request sebelumnya.
        $this->postJson('/cart/update', ['cart' => []])
            ->assertOk()
            ->assertExactJson(['items' => [], 'total' => 0]);
    }

    public function test_harga_dari_browser_diabaikan(): void
    {
        $this->postJson('/cart/update', [
            'cart' => ['1' => 2],
            'price' => 1,
            'total' => 1,
        ])->assertOk()->assertJsonPath('items.0.price', 5000)
            ->assertJsonPath('total', 10000);
    }

    public function test_produk_tidak_dikenal_ditolak(): void
    {
        $this->postJson('/cart/add', ['cart' => [], 'product_id' => 99])
            ->assertNotFound();
    }

    public function test_jumlah_tidak_valid_ditolak(): void
    {
        $this->postJson('/cart/update', ['cart' => ['1' => 0]])->assertUnprocessable();
        $this->postJson('/cart/update', ['cart' => ['1' => 100]])->assertUnprocessable();
        $this->postJson('/cart/add', ['product_id' => 1])->assertUnprocessable();
    }

    public function test_jumlah_maksimal_99(): void
    {
        $this->postJson('/cart/add', ['cart' => ['3' => 99], 'product_id' => 3])
            ->assertOk()
            ->assertJsonPath('items.0.quantity', 99);
    }
}
