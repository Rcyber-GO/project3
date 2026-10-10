<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class OnlineStoreTest extends TestCase
{
    use RefreshDatabase;

    public function test_visitors_can_browse_ten_products_without_logging_in(): void
    {
        $this->seed();

        $this->get('/')
            ->assertOk()
            ->assertSee('Buku Tulis')
            ->assertSee('Stabilo Kuning');

        $this->assertDatabaseCount('products', 10);
    }

    public function test_guest_cannot_add_items_and_authenticated_user_can(): void
    {
        $product = $this->makeProduct();

        $this->post(route('cart.add', $product))
            ->assertRedirect(route('login'));

        $user = $this->makeUser();
        $this->actingAs($user)
            ->post(route('cart.add', $product))
            ->assertRedirect();

        $this->get(route('cart.index'))
            ->assertOk()
            ->assertSee('Pensil')
            ->assertSee('Rp 2.500');
    }

    public function test_checkout_persists_order_details_decrements_stock_and_empties_cart(): void
    {
        $user = $this->makeUser();
        $product = $this->makeProduct(['stok' => 8]);

        $this->actingAs($user)
            ->withSession(['cart' => [$product->id_barang => 3]])
            ->post(route('checkout'), [
                'alamat_pengiriman' => 'Jl. Contoh No. 1, Jakarta',
                'price' => 1,
                'total' => 1,
            ])
            ->assertRedirect();

        $this->assertDatabaseCount('orders', 1);
        $this->assertDatabaseHas('order_details', [
            'id_barang' => $product->id_barang,
            'harga_satuan' => '2500.00',
            'jumlah_beli' => 3,
        ]);
        $this->assertSame(5, $product->fresh()->stok);
        $this->assertSame([], session('cart', []));

        $order = Order::firstOrFail();
        $this->assertSame('7500.00', $order->total_harga);
    }

    public function test_checkout_rejects_quantities_above_current_stock_without_creating_order(): void
    {
        $user = $this->makeUser();
        $product = $this->makeProduct(['stok' => 2]);

        $this->actingAs($user)
            ->withSession(['cart' => [$product->id_barang => 3]])
            ->post(route('checkout'), [
                'alamat_pengiriman' => 'Jl. Contoh No. 1, Jakarta',
            ])
            ->assertSessionHasErrors('cart');

        $this->assertDatabaseCount('orders', 0);
        $this->assertSame(2, $product->fresh()->stok);
    }

    public function test_users_can_only_view_their_own_order_details(): void
    {
        $owner = $this->makeUser();
        $otherUser = $this->makeUser(['username' => 'siti']);
        $order = Order::create([
            'id_order' => 'ORDERTEST000001',
            'id_user' => $owner->id_user,
            'tanggal_order' => now(),
            'total_harga' => 0,
            'alamat_pengiriman' => 'Alamat pemilik',
        ]);

        $this->actingAs($otherUser)
            ->get(route('orders.show', $order))
            ->assertNotFound();
    }

    public function test_user_can_register_and_password_is_hashed(): void
    {
        $this->post(route('register.store'), [
            'nama_lengkap' => 'Dewi Putri',
            'email' => 'dewi@example.test',
            'username' => 'dewi',
            'no_hp' => '081234567891',
            'alamat' => 'Jl. Mawar No. 2',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertRedirect(route('products.index'));

        $user = User::where('username', 'dewi')->firstOrFail();
        $this->assertAuthenticatedAs($user);
        $this->assertTrue(Hash::check('password123', $user->password));
        $this->assertNotSame('password123', $user->password);
    }

    private function makeUser(array $attributes = []): User
    {
        static $sequence = 1;

        return User::create([
            'id_user' => 'UTEST'.str_pad((string) $sequence++, 10, '0', STR_PAD_LEFT),
            'nama_lengkap' => 'Budi Santoso',
            'email' => 'user'.$sequence.'@example.test',
            'username' => 'budi'.$sequence,
            'password' => 'password123',
            'no_hp' => '081234567890',
            'alamat' => 'Jl. Contoh No. 1, Jakarta',
            ...$attributes,
        ]);
    }

    private function makeProduct(array $attributes = []): Product
    {
        static $sequence = 1;

        return Product::create([
            'id_barang' => 'PTEST'.str_pad((string) $sequence++, 5, '0', STR_PAD_LEFT),
            'nama_barang' => 'Pensil',
            'deskripsi' => 'Pensil untuk tes.',
            'harga' => 2500,
            'stok' => 10,
            'gambar' => 'stationery.svg',
            ...$attributes,
        ]);
    }
}
