<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\QuoteRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class QuoteRequestController extends Controller
{
    public function store(Request $request, Product $product): RedirectResponse
    {
        abort_unless($product->isPubliclyVisible() && $product->supportsQuote(), 422, __('site.validation.quote_unavailable'));
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'], 'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:40'], 'company' => ['nullable', 'string', 'max:255'], 'message' => ['nullable', 'string', 'max:3000'],
        ]);
        $data['user_id'] = $request->user()?->id;
        $data['product_snapshot'] = ['id' => $product->id, 'slug' => $product->slug, 'title' => $product->title, 'price_irr' => $product->price_irr, 'sales_mode' => $product->effectiveSalesMode()];
        QuoteRequest::query()->create($data);

        return back()->with('success', __('site.messages.quote_submitted'));
    }
}
