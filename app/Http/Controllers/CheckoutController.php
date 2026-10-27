<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderEvent;
use App\Models\OrderItem;
use App\Models\Product;
use App\Services\Cart;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Illuminate\Validation\ValidationException;

class CheckoutController extends Controller
{
    public function create(Cart $cart): View
    {
        return view('public.checkout', ['lines' => $cart->lines(), 'total' => $cart->total()]);
    }

    public function store(Request $request, Cart $cart): RedirectResponse
    {
        $data = $request->validate([
            'customer_name' => ['required', 'string', 'max:120'], 'email' => ['required', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:40'], 'address' => ['nullable', 'string', 'max:2000'],
        ]);
        $cartItems = session('crane_cart', []);
        if ($cartItems === []) {
            throw ValidationException::withMessages(['cart' => __('site.validation.cart_empty')]);
        }

        $order = DB::transaction(function () use ($request, $data, $cartItems) {
            $products = Product::query()->lockForUpdate()->publiclyVisible()->whereIn('id', array_keys($cartItems))->get()->keyBy('id');
            if ($products->count() !== count($cartItems)) {
                throw ValidationException::withMessages(['cart' => __('site.validation.cart_product_unavailable')]);
            }
            $lines = $products->map(function (Product $product) use ($cartItems): array {
                if (! $product->supportsDirectSale()) {
                    throw ValidationException::withMessages(['cart' => __('site.validation.cart_direct_unavailable')]);
                }
                $quantity = max(1, min(99, (int) $cartItems[$product->id]));

                return ['product' => $product, 'quantity' => $quantity, 'line_total' => $product->price_irr * $quantity];
            });
            $total = $lines->sum('line_total');
            $order = Order::query()->create([...$data, 'user_id' => $request->user()?->id, 'number' => 'SOR-'.now()->format('Ymd').'-'.Str::upper(Str::random(6)), 'subtotal_irr' => $total, 'total_irr' => $total]);
            foreach ($lines as $line) {
                OrderItem::query()->create(['order_id' => $order->id, 'product_id' => $line['product']->id, 'product_title' => $line['product']->title, 'quantity' => $line['quantity'], 'unit_price_irr' => $line['product']->price_irr, 'line_total_irr' => $line['line_total']]);
            }
            OrderEvent::query()->create(['order_id' => $order->id, 'actor_id' => $request->user()?->id, 'event' => 'order_created', 'to_status' => 'pending_payment']);

            return $order;
        });

        $cart->clear();
        session()->put('order_access.'.$order->id, true);

        return redirect()->route('orders.show', $order)->with('success', __('site.messages.order_created'));
    }

    public function show(Request $request, Order $order): View
    {
        $isOwner = $order->user_id !== null && $request->user()?->id === $order->user_id;
        $hasGuestSession = (bool) session('order_access.'.$order->id);
        $canManageSales = $request->user()?->canManageSales() ?? false;
        abort_unless($isOwner || $hasGuestSession || $canManageSales, 403);
        $order->load(['items', 'payment', 'events.actor']);

        return view('public.order', compact('order'));
    }
}
