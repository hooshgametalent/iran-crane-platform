<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\Crane;
use App\Models\CraneServiceArea;
use App\Models\CraneType;
use App\Models\Organization;
use App\Models\PlatformSetting;
use App\Models\Province;
use App\Models\RentalBooking;
use App\Models\RentalQuote;
use App\Models\RentalRequest;
use App\Models\Role;
use App\Models\User;
use App\Support\PricingSettings;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use LogicException;

class DemoDataSeeder extends Seeder
{
    /**
     * Seed safe, repeatable marketplace demonstration data.
     */
    public function run(): void
    {
        throw_if(app()->isProduction(), LogicException::class, 'دادهٔ نمایشی نباید در محیط تولید بارگذاری شود.');

        DB::transaction(function (): void {
            $roles = $this->seedRoles();
            $cities = $this->seedLocations();
            $this->seedSettings();

            $admin = $this->user('مدیر سامانه', 'admin@example.test');
            $owner = $this->user('علی رضایی', 'owner@example.test');
            $customer = $this->user('مریم احمدی', 'customer@example.test');

            $admin->roles()->syncWithoutDetaching([$roles['admin']->id]);
            $owner->roles()->syncWithoutDetaching([$roles['owner']->id]);
            $customer->roles()->syncWithoutDetaching([$roles['customer']->id]);

            $provider = Organization::query()->updateOrCreate(
                ['slug' => 'tehran-crane-rental'],
                [
                    'name' => 'اجاره جرثقیل تهران',
                    'type' => 'company',
                    'status' => 'active',
                    'description' => 'دادهٔ نمایشی برای مالک جرثقیل.',
                ],
            );
            $requester = Organization::query()->updateOrCreate(
                ['slug' => 'sample-construction-company'],
                [
                    'name' => 'شرکت ساختمانی نمونه',
                    'type' => 'company',
                    'status' => 'active',
                    'description' => 'دادهٔ نمایشی برای کارفرما.',
                ],
            );

            $provider->users()->syncWithoutDetaching([$owner->id]);
            $requester->users()->syncWithoutDetaching([$customer->id]);

            $types = $this->seedCraneTypes();
            $cranes = $this->seedCranes($provider, $types, $cities);
            $this->seedRequestsAndBookings($customer, $requester, $types, $cities, $cranes, $provider, $owner);
        });
    }

    /**
     * @return array<string, Role>
     */
    private function seedRoles(): array
    {
        $definitions = [
            'admin' => ['label' => 'مدیر سامانه', 'description' => 'مدیریت تنظیمات و داده‌های پلتفرم.'],
            'owner' => ['label' => 'مالک جرثقیل', 'description' => 'مدیریت جرثقیل‌ها و ارسال پیشنهاد قیمت.'],
            'customer' => ['label' => 'کارفرما', 'description' => 'ثبت درخواست اجاره و پذیرش پیشنهاد.'],
        ];

        $roles = [];

        foreach ($definitions as $name => $attributes) {
            $roles[$name] = Role::query()->updateOrCreate(['name' => $name], $attributes);
        }

        return $roles;
    }

    /**
     * @return array<string, City>
     */
    private function seedLocations(): array
    {
        $definitions = [
            'tehran' => ['province' => 'تهران', 'province_slug' => 'tehran', 'name' => 'تهران'],
            'karaj' => ['province' => 'البرز', 'province_slug' => 'alborz', 'name' => 'کرج'],
            'isfahan' => ['province' => 'اصفهان', 'province_slug' => 'isfahan', 'name' => 'اصفهان'],
            'mashhad' => ['province' => 'خراسان رضوی', 'province_slug' => 'razavi-khorasan', 'name' => 'مشهد'],
            'ahvaz' => ['province' => 'خوزستان', 'province_slug' => 'khuzestan', 'name' => 'اهواز'],
            'shiraz' => ['province' => 'فارس', 'province_slug' => 'fars', 'name' => 'شیراز'],
        ];

        $cities = [];

        foreach ($definitions as $slug => $definition) {
            $province = Province::query()->firstOrCreate(
                ['slug' => $definition['province_slug']],
                ['name' => $definition['province']],
            );

            $cities[$slug] = City::query()->firstOrCreate(
                ['province_id' => $province->id, 'slug' => $slug],
                ['name' => $definition['name']],
            );
        }

        return $cities;
    }

    private function seedSettings(): void
    {
        PlatformSetting::query()->firstOrCreate(
            ['setting_key' => 'pricing.default_currency'],
            [
                'setting_value' => 'IRR',
                'value_type' => 'string',
                'group_name' => 'pricing',
                'description' => 'واحد پایهٔ مبالغ. مدیر سامانه می‌تواند آن را تغییر دهد.',
            ],
        );
    }

    /**
     * @return array<string, CraneType>
     */
    private function seedCraneTypes(): array
    {
        $definitions = [
            'mobile-crane' => ['name' => 'جرثقیل موبایل', 'sort_order' => 1],
            'truck-crane' => ['name' => 'جرثقیل کفی', 'sort_order' => 2],
            'telescopic-crane' => ['name' => 'جرثقیل تلسکوپی', 'sort_order' => 3],
            'tower-crane' => ['name' => 'تاورکرین', 'sort_order' => 4],
        ];

        $types = [];

        foreach ($definitions as $slug => $attributes) {
            $types[$slug] = CraneType::query()->updateOrCreate(
                ['slug' => $slug],
                [...$attributes, 'is_active' => true],
            );
        }

        return $types;
    }

    /**
     * @param  array<string, CraneType>  $types
     * @param  array<string, City>  $cities
     * @return array<string, Crane>
     */
    private function seedCranes(Organization $provider, array $types, array $cities): array
    {
        $definitions = [
            'mobile-50' => [
                'crane_type_id' => $types['mobile-crane']->id,
                'owner_reference' => 'DEMO-MOB-50',
                'manufacturer' => 'Demag',
                'model' => 'AC 50',
                'manufacture_year' => 2018,
                'capacity_kg' => 50_000,
                'boom_length_mm' => 40_000,
                'description' => 'جرثقیل موبایل ۵۰ تن برای پروژه‌های شهری و صنعتی.',
                'status' => 'active',
                'is_featured' => true,
                'areas' => ['tehran', 'karaj'],
            ],
            'telescopic-100' => [
                'crane_type_id' => $types['telescopic-crane']->id,
                'owner_reference' => 'DEMO-TEL-100',
                'manufacturer' => 'Liebherr',
                'model' => 'LTM 1100',
                'manufacture_year' => 2020,
                'capacity_kg' => 100_000,
                'boom_length_mm' => 52_000,
                'description' => 'جرثقیل تلسکوپی ۱۰۰ تن برای پروژه‌های سنگین.',
                'status' => 'active',
                'is_featured' => true,
                'areas' => ['tehran', 'isfahan', 'ahvaz'],
            ],
        ];

        $cranes = [];

        foreach ($definitions as $key => $definition) {
            $areas = $definition['areas'];
            unset($definition['areas']);

            $cranes[$key] = Crane::query()->updateOrCreate(
                [
                    'organization_id' => $provider->id,
                    'owner_reference' => $definition['owner_reference'],
                ],
                $definition,
            );

            foreach ($areas as $citySlug) {
                $city = $cities[$citySlug];

                CraneServiceArea::query()->updateOrCreate(
                    ['crane_id' => $cranes[$key]->id, 'area_key' => "city:{$city->id}"],
                    ['province_id' => $city->province_id, 'city_id' => $city->id],
                );
            }
        }

        return $cranes;
    }

    /**
     * @param  array<string, CraneType>  $types
     * @param  array<string, City>  $cities
     * @param  array<string, Crane>  $cranes
     */
    private function seedRequestsAndBookings(
        User $customer,
        Organization $requester,
        array $types,
        array $cities,
        array $cranes,
        Organization $provider,
        User $owner,
    ): void {
        $request = RentalRequest::query()->updateOrCreate(
            ['user_id' => $customer->id, 'title' => 'اجاره جرثقیل ۵۰ تن برای پروژه تهران'],
            [
                'organization_id' => $requester->id,
                'crane_type_id' => $types['mobile-crane']->id,
                'city_id' => $cities['tehran']->id,
                'description' => 'جابه‌جایی تجهیزات در یک پروژهٔ ساختمانی در تهران.',
                'required_capacity_kg' => 45_000,
                'starts_at' => now()->addDays(7)->startOfDay(),
                'ends_at' => now()->addDays(9)->endOfDay(),
                'status' => 'closed',
            ],
        );

        RentalRequest::query()->updateOrCreate(
            ['user_id' => $customer->id, 'title' => 'اجاره جرثقیل تلسکوپی برای پروژه اصفهان'],
            [
                'organization_id' => $requester->id,
                'crane_type_id' => $types['telescopic-crane']->id,
                'city_id' => $cities['isfahan']->id,
                'description' => 'انتقال سازه‌های سنگین در پروژهٔ صنعتی اصفهان.',
                'required_capacity_kg' => 90_000,
                'starts_at' => now()->addDays(14)->startOfDay(),
                'ends_at' => now()->addDays(16)->endOfDay(),
                'status' => 'open',
            ],
        );

        $quote = RentalQuote::query()->updateOrCreate(
            ['rental_request_id' => $request->id, 'crane_id' => $cranes['mobile-50']->id],
            [
                'organization_id' => $provider->id,
                'submitted_by_user_id' => $owner->id,
                'amount' => 250_000_000,
                'currency' => app(PricingSettings::class)->defaultCurrency(),
                'billing_unit' => 'day',
                'transport_fee' => 40_000_000,
                'terms' => 'هزینهٔ اپراتور در مبلغ روزانه لحاظ شده است.',
                'expires_at' => now()->addDays(5),
                'status' => 'accepted',
            ],
        );

        RentalBooking::query()->updateOrCreate(
            ['rental_quote_id' => $quote->id],
            [
                'rental_request_id' => $request->id,
                'crane_id' => $cranes['mobile-50']->id,
                'customer_user_id' => $customer->id,
                'customer_organization_id' => $requester->id,
                'provider_organization_id' => $provider->id,
                'starts_at' => $request->starts_at,
                'ends_at' => $request->ends_at,
                'quoted_amount' => $quote->amount,
                'transport_fee' => $quote->transport_fee,
                'total_amount' => $quote->amount + $quote->transport_fee,
                'currency' => $quote->currency,
                'status' => 'confirmed',
            ],
        );
    }

    private function user(string $name, string $email): User
    {
        return User::query()->updateOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
            ],
        );
    }
}
