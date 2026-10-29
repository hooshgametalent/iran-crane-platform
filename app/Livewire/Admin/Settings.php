<?php

namespace App\Livewire\Admin;

use App\Models\PlatformSetting;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Component;

class Settings extends Component
{
    public string $defaultCurrency = '';

    public int $lockVersion;

    public bool $saved = false;

    /**
     * Load the default currency and its optimistic-lock version.
     */
    public function mount(): void
    {
        $setting = PlatformSetting::query()->firstOrCreate(
            ['setting_key' => 'pricing.default_currency'],
            [
                'setting_value' => 'IRR',
                'value_type' => 'string',
                'group_name' => 'pricing',
                'description' => 'واحد پایهٔ مبالغ جدید.',
            ],
        );

        $this->defaultCurrency = is_string($setting->setting_value) ? $setting->setting_value : 'IRR';
        $this->lockVersion = $setting->lock_version;
    }

    /**
     * Update the currency used for new pricing records.
     */
    public function save(): void
    {
        $this->defaultCurrency = Str::upper(trim($this->defaultCurrency));
        $validated = $this->validate([
            'defaultCurrency' => ['required', 'regex:/^[A-Z]{3}$/'],
        ]);

        $updated = DB::table('platform_settings')
            ->where('setting_key', 'pricing.default_currency')
            ->where('lock_version', $this->lockVersion)
            ->update([
                'setting_value' => json_encode($validated['defaultCurrency'], JSON_THROW_ON_ERROR),
                'updated_at' => now(),
                'lock_version' => DB::raw('lock_version + 1'),
            ]);

        if ($updated !== 1) {
            $this->saved = false;
            $this->addError('defaultCurrency', 'این تنظیم هم‌زمان تغییر کرده است. صفحه را تازه کنید و دوباره تلاش کنید.');

            return;
        }

        $this->lockVersion++;
        $this->saved = true;
    }

    /**
     * Render the administrative settings page.
     */
    public function render(): View
    {
        return view('livewire.admin.settings')->title('تنظیمات پنل مدیریت');
    }
}
