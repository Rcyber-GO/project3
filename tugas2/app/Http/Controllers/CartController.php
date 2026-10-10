<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CartController extends Controller
{
    public function index()
    {
        $products = Product::all();
        $cart = $this->cartContents();
        $availableProducts = array_flip($products->modelKeys());

        return view('products.index', [
            'products' => $products,
            'cartCount' => array_sum(array_intersect_key($cart, $availableProducts)),
        ]);
    }

    public function cart()
    {
        $cart = $this->cartContents();
        $items = [];
        $total = 0;

        foreach ($cart as $productId => $qty) {
            $product = Product::find($productId);

            if (! $product) {
                continue;
            }

            $subtotal = $product->price * $qty;

            $items[] = [
                'product' => $product,
                'qty' => $qty,
                'subtotal' => $subtotal,
            ];

            $total += $subtotal;
        }

        return view('products.cart', [
            'items' => $items,
            'total' => $total,
            'cartCount' => array_sum(array_column($items, 'qty')),
        ]);
    }

    public function add(Product $product)
    {
        $cart = $this->cartContents();
        $qty = $cart[$product->id] ?? 0;
        $newQty = $qty + 1;

        if ($product->stock < 1 || $newQty > $product->stock) {
            return back()->with('error', 'Stok tidak mencukupi untuk produk ini.');
        }

        $cart[$product->id] = $newQty;

        session()->put('cart', $cart);

        return redirect()->route('products.index')->with('success', 'Produk ditambahkan ke keranjang.');
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'change' => ['required', 'integer', Rule::in([-1, 1])],
        ]);
        $cart = $this->cartContents();
        $currentQty = $cart[$product->id] ?? 0;

        if ($currentQty === 0) {
            return redirect()->route('cart.index')->with('error', 'Produk tersebut tidak ada di keranjang.');
        }

        $change = (int) $validated['change'];
        $newQty = $currentQty + $change;

        if ($newQty <= 0) {
            unset($cart[$product->id]);
        } else {
            if ($newQty > $product->stock) {
                return redirect()->route('cart.index')->with('error', 'Stok tidak mencukupi untuk produk ini.');
            }

            $cart[$product->id] = $newQty;
        }

        session()->put('cart', $cart);

        return redirect()->route('cart.index');
    }

    public function remove(Product $product)
    {
        $cart = $this->cartContents();

        unset($cart[$product->id]);

        session()->put('cart', $cart);

        return redirect()->route('cart.index');
    }

    public function clear()
    {
        session()->forget('cart');

        return redirect()->route('cart.index');
    }

    private function cartContents(): array
    {
        $storedCart = session()->get('cart', []);
        if (! is_array($storedCart)) {
            return [];
        }

        $cart = [];
        foreach ($storedCart as $productId => $quantity) {
            $validProductId = filter_var($productId, FILTER_VALIDATE_INT);
            $validQuantity = is_int($quantity)
                || (is_string($quantity) && ctype_digit($quantity));

            if ($validProductId === false || $validProductId < 1 || ! $validQuantity || (int) $quantity < 1) {
                continue;
            }

            $cart[$validProductId] = (int) $quantity;
        }

        return $cart;
    }
}
