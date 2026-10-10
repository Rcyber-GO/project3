<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_listing_page_loads(): void
    {
        $product = Product::create([
            'name' => 'Buku Tulis',
            'price' => 5000,
            'stock' => 10,
        ]);

        $this->get('/')->assertOk()->assertSee('Toko Alat Tulis')->assertSee('Buku Tulis');
        $this->assertDatabaseHas('barang', ['id' => $product->id, 'name' => 'Buku Tulis']);
    }

    public function test_user_can_add_product_to_cart_and_view_total(): void
    {
        $product = Product::create([
            'name' => 'Pulpen',
            'price' => 3000,
            'stock' => 10,
        ]);

        $this->post(route('cart.add', $product->id))
            ->assertRedirect(route('products.index'));

        $this->assertSame([$product->id => 1], session('cart'));

        $this->get(route('cart.index'))
            ->assertOk()
            ->assertSee('Pulpen')
            ->assertSee('Rp 3.000')
            ->assertSee('Total');
    }

    public function test_user_can_update_and_remove_items_from_cart(): void
    {
        $product = Product::create([
            'name' => 'Penggaris',
            'price' => 4000,
            'stock' => 5,
        ]);

        $this->withSession([
            'cart' => [
                $product->id => 2,
            ],
        ])->post(route('cart.update', $product->id), ['change' => -1])
            ->assertRedirect(route('cart.index'));

        $this->withSession([
            'cart' => [
                $product->id => 1,
            ],
        ])->post(route('cart.remove', $product->id))
            ->assertRedirect(route('cart.index'));

        $this->withSession([])->get(route('cart.index'))->assertSee('Keranjang masih kosong.');
    }

    public function test_update_rejects_invalid_changes_and_keeps_cart_unchanged(): void
    {
        $product = Product::create([
            'name' => 'Buku Gambar',
            'price' => 7000,
            'stock' => 3,
        ]);

        $this->withSession(['cart' => [$product->id => 2]])
            ->post(route('cart.update', $product->id), ['change' => 999])
            ->assertSessionHasErrors('change');

        $this->assertSame([$product->id => 2], session('cart'));
    }

    public function test_add_rejects_products_with_no_stock(): void
    {
        $product = Product::create([
            'name' => 'Map',
            'price' => 2000,
            'stock' => 0,
        ]);

        $this->post(route('cart.add', $product->id))
            ->assertRedirect()
            ->assertSessionHas('error');

        $this->assertSame([], session('cart', []));
    }
}
