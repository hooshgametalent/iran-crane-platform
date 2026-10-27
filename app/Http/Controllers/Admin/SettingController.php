<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\SiteSettings;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function edit(Request $request, SiteSettings $settings): View
    {
        abort_unless($request->user()->role === 'super_admin', 403);

        return view('admin.settings', compact('settings'));
    }

    public function update(Request $request, SiteSettings $settings): RedirectResponse
    {
        abort_unless($request->user()->role === 'super_admin', 403);
        $data = $request->validate([
            'brand_name_fa' => ['required', 'string', 'max:120'],
            'brand_name_en' => ['required', 'string', 'max:120'],
            'brand_name_ar' => ['required', 'string', 'max:120'],
            'sales_mode' => ['required', 'in:direct,quote,both'], 'demo_mode' => ['boolean'],
        ]);

        DB::transaction(function () use ($data, $request, $settings): void {
            if (in_array($data['sales_mode'], ['direct', 'both'], true)) {
                $invalidProducts = Product::query()->active()->where('sales_mode', 'inherit')
                    ->where(fn ($query) => $query->whereNull('price_irr')->orWhere('price_irr', '<=', 0))
                    ->lockForUpdate()->get(['id', 'slug']);

                if ($invalidProducts->isNotEmpty()) {
                    throw ValidationException::withMessages([
                        'sales_mode' => __('site.validation.global_direct_price_required', ['products' => $invalidProducts->pluck('slug')->implode(', ')]),
                    ]);
                }
            }

            $settings->put('brand.name', ['fa' => $data['brand_name_fa'], 'en' => $data['brand_name_en'], 'ar' => $data['brand_name_ar']]);
            $settings->put('store.sales_mode', $data['sales_mode']);
            $settings->put('site.demo_mode', $request->boolean('demo_mode'));
        });

        return back()->with('success', __('site.messages.settings_saved'));
    }
}
