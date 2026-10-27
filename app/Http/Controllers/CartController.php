<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\Cart;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CartController extends Controller
{
    public function index(Cart $cart): View { return view('public.cart', ['lines' => $cart->lines(), 'total' => $cart->total()]); }

    public function store(Request $request, Product $product, Cart $cart): RedirectResponse
    {
        abort_unless($product->isPubliclyVisible() && $product->supportsDirectSale(), 422, __('site.validation.product_direct_unavailable'));
        $data = $request->validate(['quantity' => ['nullable', 'integer', 'min:1', 'max:99']]);
        $cart->add($product, $data['quantity'] ?? 1);

        return back()->with('success', __('site.messages.cart_added'));
    }

    public function update(Request $request, Product $product, Cart $cart): RedirectResponse
    {
        $data = $request->validate(['quantity' => ['required', 'integer', 'min:0', 'max:99']]);
        $cart->update($product->id, $data['quantity']);

        return back()->with('success', __('site.messages.cart_updated'));
    }
}
