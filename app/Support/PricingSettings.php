<?php

namespace App\Support;

use App\Models\PlatformSetting;

class PricingSettings
{
    public const FALLBACK_CURRENCY = 'IRR';

    /**
     * Get the administrator-selected currency for newly created prices.
     */
    public function defaultCurrency(): string
    {
        $value = PlatformSetting::query()
            ->where('setting_key', 'pricing.default_currency')
            ->first()?->setting_value;

        return is_string($value) && preg_match('/^[A-Z]{3}$/', $value) === 1
            ? $value
            : self::FALLBACK_CURRENCY;
    }
}
