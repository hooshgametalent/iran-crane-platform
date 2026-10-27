<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Schema;

class SiteSettings
{
    /** @var array<string, mixed> */
    private const DEFAULTS = [
        'brand.name' => ['fa' => 'سوری', 'en' => 'Souri', 'ar' => 'سوري'],
        'site.demo_mode' => false,
        'store.sales_mode' => 'both',
    ];

    public function get(string $key): mixed
    {
        if (! Schema::hasTable('settings')) {
            return self::DEFAULTS[$key] ?? null;
        }

        return Setting::query()->where('key', $key)->value('value') ?? self::DEFAULTS[$key] ?? null;
    }

    public function put(string $key, mixed $value): void
    {
        if (! Schema::hasTable('settings')) {
            return;
        }

        Setting::query()->updateOrCreate(['key' => $key], ['value' => $value]);
    }

    public function brandName(?string $locale = null): string
    {
        $values = $this->get('brand.name');
        $locale ??= app()->getLocale();

        return is_array($values) ? ($values[$locale] ?? $values['fa'] ?? 'سوری') : 'سوری';
    }

    public function demoMode(): bool
    {
        return (bool) $this->get('site.demo_mode');
    }

    public function salesMode(): string
    {
        $mode = (string) $this->get('store.sales_mode');

        return in_array($mode, ['direct', 'quote', 'both'], true) ? $mode : 'quote';
    }
}
