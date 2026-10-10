<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class StoreController extends Controller
{
    public function index(): View
    {
        return view('store.index', [
            'products' => Product::query()->orderBy('nama_barang')->get(),
        ]);
    }

    public function cart(): View
    {
        $cart = $this->cartContents();
        $products = Product::query()->whereIn('id_barang', array_keys($cart))->get()->keyBy('id_barang');
        $items = [];
        $totalCents = 0;

        foreach ($cart as $productId => $quantity) {
            $product = $products->get($productId);
            if (! $product) {
                continue;
            }

            $subtotalCents = $this->priceInCents($product) * $quantity;
            $items[] = [
                'product' => $product,
                'quantity' => $quantity,
                'subtotal' => $subtotalCents,
            ];
            $totalCents += $subtotalCents;
        }

        return view('store.cart', [
            'items' => $items,
            'total' => $totalCents,
            'address' => auth()->user()->alamat,
        ]);
    }

    public function add(Product $product): RedirectResponse
    {
        $cart = $this->cartContents();

        if ($product->stok < 1) {
            return back()->with('error', 'Produk ini sedang habis.');
        }

        $newQuantity = ($cart[$product->id_barang] ?? 0) + 1;
        if ($newQuantity > $product->stok) {
            return back()->with('error', 'Jumlah di keranjang tidak boleh melebihi stok tersedia.');
        }

        $cart[$product->id_barang] = $newQuantity;
        session()->put('cart', $cart);

        return back()->with('success', $product->nama_barang.' ditambahkan ke keranjang.');
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $validated = $request->validate([
            'quantity' => ['required', 'integer', 'min:1', 'max:'.max(1, $product->stok)],
        ]);
        $cart = $this->cartContents();

        if (! array_key_exists($product->id_barang, $cart)) {
            return redirect()->route('cart.index')->with('error', 'Produk tersebut tidak ada di keranjang.');
        }

        if ($validated['quantity'] > $product->stok) {
            return back()->with('error', 'Jumlah di keranjang tidak boleh melebihi stok tersedia.');
        }

        $cart[$product->id_barang] = (int) $validated['quantity'];
        session()->put('cart', $cart);

        return redirect()->route('cart.index')->with('success', 'Jumlah produk diperbarui.');
    }

    public function remove(Product $product): RedirectResponse
    {
        $cart = $this->cartContents();
        unset($cart[$product->id_barang]);
        session()->put('cart', $cart);

        return redirect()->route('cart.index')->with('success', 'Produk dihapus dari keranjang.');
    }

    public function checkout(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'alamat_pengiriman' => ['required', 'string', 'max:2000'],
        ]);
        $cart = $this->cartContents();

        if ($cart === []) {
            return redirect()->route('cart.index')->with('error', 'Keranjang masih kosong.');
        }

        $order = DB::transaction(function () use ($cart, $validated): Order {
            $products = Product::query()
                ->whereIn('id_barang', array_keys($cart))
                ->orderBy('id_barang')
                ->lockForUpdate()
                ->get()
                ->keyBy('id_barang');

            if ($products->count() !== count($cart)) {
                throw ValidationException::withMessages([
                    'cart' => 'Salah satu produk di keranjang sudah tidak tersedia.',
                ]);
            }

            $totalCents = 0;
            foreach ($cart as $productId => $quantity) {
                $product = $products->get($productId);
                if ($quantity > $product->stok) {
                    throw ValidationException::withMessages([
                        'cart' => "Stok {$product->nama_barang} tidak mencukupi. Silakan perbarui keranjang.",
                    ]);
                }

                $totalCents += $this->priceInCents($product) * $quantity;
            }

            $order = Order::create([
                'id_order' => $this->newOrderId(),
                'id_user' => auth()->user()->id_user,
                'tanggal_order' => now(),
                'total_harga' => $this->formatCents($totalCents),
                'alamat_pengiriman' => $validated['alamat_pengiriman'],
            ]);

            foreach ($cart as $productId => $quantity) {
                $product = $products->get($productId);
                OrderDetail::create([
                    'id_order' => $order->id_order,
                    'id_barang' => $product->id_barang,
                    'harga_satuan' => $product->harga,
                    'jumlah_beli' => $quantity,
                ]);

                $product->decrement('stok', $quantity);
            }

            return $order;
        });

        session()->forget('cart');

        return redirect()->route('orders.show', $order)->with('success', 'Checkout berhasil. Pesanan Anda telah dibuat.');
    }

    public function orders(): View
    {
        $orders = Order::query()
            ->where('id_user', auth()->user()->id_user)
            ->with('details.product')
            ->latest('tanggal_order')
            ->get();

        return view('orders.index', ['orders' => $orders]);
    }

    public function showOrder(Order $order): View
    {
        abort_unless($order->id_user === auth()->user()->id_user, 404);

        return view('orders.show', [
            'order' => $order->load('details.product'),
        ]);
    }

    private function cartContents(): array
    {
        $rawCart = session()->get('cart', []);
        if (! is_array($rawCart)) {
            return [];
        }

        $cart = [];

        foreach ($rawCart as $productId => $quantity) {
            if (
                is_string($productId)
                && $productId !== ''
                && (is_int($quantity) || (is_string($quantity) && ctype_digit($quantity)))
                && (int) $quantity > 0
            ) {
                $cart[$productId] = (int) $quantity;
            }
        }

        return $cart;
    }

    private function priceInCents(Product $product): int
    {
        [$whole, $fraction] = array_pad(explode('.', (string) $product->harga, 2), 2, '00');

        return ((int) $whole * 100) + (int) str_pad(substr($fraction, 0, 2), 2, '0');
    }

    private function formatCents(int $amount): string
    {
        return intdiv($amount, 100).'.'.str_pad((string) ($amount % 100), 2, '0', STR_PAD_LEFT);
    }

    private function newOrderId(): string
    {
        do {
            $id = Str::upper(Str::random(15));
        } while (Order::query()->whereKey($id)->exists());

        return $id;
    }
}
