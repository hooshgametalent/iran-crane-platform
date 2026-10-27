<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\CaseStudy;
use App\Models\Category;
use App\Models\Product;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        Setting::query()->updateOrCreate(['key' => 'brand.name'], ['value' => ['fa' => 'سوری', 'en' => 'Souri', 'ar' => 'سوري']]);
        Setting::query()->firstOrCreate(['key' => 'site.demo_mode'], ['value' => app()->environment(['local', 'testing'])]);
        Setting::query()->firstOrCreate(['key' => 'store.sales_mode'], ['value' => 'both']);

        $mobile = Category::query()->updateOrCreate(['slug' => 'mobile-cranes'], ['title' => ['fa' => 'جرثقیل‌های متحرک', 'en' => 'Mobile cranes', 'ar' => 'الرافعات المتنقلة'], 'is_active' => true, 'is_demo' => true, 'sort_order' => 10]);
        $industrial = Category::query()->updateOrCreate(['slug' => 'industrial-cranes'], ['title' => ['fa' => 'جرثقیل‌های صنعتی', 'en' => 'Industrial cranes', 'ar' => 'الرافعات الصناعية'], 'is_active' => true, 'is_demo' => true, 'sort_order' => 20]);
        $telescopic = Category::query()->updateOrCreate(['slug' => 'telescopic'], ['parent_id' => $mobile->id, 'title' => ['fa' => 'تلسکوپی', 'en' => 'Telescopic', 'ar' => 'تلسكوبية'], 'is_active' => true, 'is_demo' => true, 'sort_order' => 10]);

        $products = collect([
            ['slug' => 'souri-st-30', 'category_id' => $telescopic->id, 'title' => ['fa' => 'جرثقیل تلسکوپی ST-30', 'en' => 'ST-30 telescopic crane', 'ar' => 'رافعة ST-30 تلسكوبية'], 'short_description' => ['fa' => 'راهکار ۳۰ تنی برای پروژه‌های شهری و کارگاهی.', 'en' => 'A 30-ton solution for urban and workshop projects.', 'ar' => 'حل بقدرة 30 طناً للمشاريع الحضرية وورش العمل.'], 'description' => ['fa' => 'ST-30 برای کاربری‌های روزانه طراحی شده است. مشخصات نهایی و زمان تحویل پس از بررسی نیاز پروژه اعلام می‌شود.', 'en' => 'ST-30 is designed for daily work. Final specifications and delivery lead time follow project review.', 'ar' => 'صممت ST-30 للعمل اليومي. تحدد المواصفات النهائية وموعد التسليم بعد مراجعة المشروع.'], 'price_irr' => 18500000000, 'sales_mode' => 'both', 'is_featured' => true],
            ['slug' => 'souri-sc-60', 'category_id' => $mobile->id, 'title' => ['fa' => 'جرثقیل کارگاهی SC-60', 'en' => 'SC-60 site crane', 'ar' => 'رافعة مواقع SC-60'], 'short_description' => ['fa' => 'پوشش پایدار برای بارهای سنگین در سایت پروژه.', 'en' => 'Stable coverage for heavy loads on site.', 'ar' => 'تغطية مستقرة للأحمال الثقيلة في الموقع.'], 'description' => ['fa' => 'برای شرایط سایت، ظرفیت و تجهیزات جانبی پیش از ثبت سفارش با تیم فروش هماهنگ می‌شود.', 'en' => 'Site conditions, capacity, and accessories are coordinated before ordering.', 'ar' => 'تنسق ظروف الموقع والسعة والملحقات قبل الطلب.'], 'price_irr' => null, 'sales_mode' => 'quote', 'is_featured' => true],
            ['slug' => 'souri-ib-10', 'category_id' => $industrial->id, 'title' => ['fa' => 'جرثقیل سقفی IB-10', 'en' => 'IB-10 overhead crane', 'ar' => 'رافعة علوية IB-10'], 'short_description' => ['fa' => 'برای خطوط تولید با نیاز به جابه‌جایی دقیق.', 'en' => 'For production lines requiring precise movement.', 'ar' => 'لخطوط الإنتاج التي تتطلب حركة دقيقة.'], 'description' => ['fa' => 'طراحی دهانه و تجهیزات با بازدید و نیازسنجی محل انجام می‌شود.', 'en' => 'Span design and equipment follow an on-site assessment.', 'ar' => 'يتم تصميم البحر والمعدات بعد تقييم الموقع.'], 'price_irr' => 9200000000, 'sales_mode' => 'inherit', 'is_featured' => true],
        ])->map(fn (array $data) => Product::query()->updateOrCreate(['slug' => $data['slug']], [...$data, 'is_active' => true, 'is_demo' => true, 'sort_order' => 10]));

        $article = Article::query()->updateOrCreate(['slug' => 'safe-lifting-planning'], ['title' => ['fa' => 'پنج گام برای برنامه‌ریزی ایمن باربرداری', 'en' => 'Five steps for safe lifting planning', 'ar' => 'خمس خطوات لتخطيط رفع آمن'], 'excerpt' => ['fa' => 'مرور ظرفیت، محیط و مسیر حرکت پیش از عملیات.', 'en' => 'Review capacity, environment, and travel before work.', 'ar' => 'راجع السعة والبيئة ومسار الحركة قبل العمل.'], 'body' => ['fa' => 'برنامه باربرداری باید وزن واقعی بار، شرایط زمین، شعاع کاری و مسیر حرکت را در نظر بگیرد. این متن نمونه برای نمایش قابلیت انتشار محتوا است.', 'en' => 'A lifting plan should account for actual load weight, ground conditions, working radius, and travel path. This is demonstration content.', 'ar' => 'يجب أن تراعي خطة الرفع وزن الحمولة الفعلي وظروف الأرض ونصف قطر العمل ومسار الحركة. هذا محتوى توضيحي.'], 'is_published' => true, 'is_demo' => true, 'published_at' => now()]);
        $article->products()->sync($products->pluck('id')->take(2));

        $study = CaseStudy::query()->updateOrCreate(['slug' => 'workshop-uptime-case'], ['title' => ['fa' => 'بهبود زمان آماده‌به‌کاری در یک کارگاه', 'en' => 'Improving workshop uptime', 'ar' => 'تحسين جاهزية ورشة عمل'], 'customer_name' => ['fa' => 'مشتری نمونه صنعتی', 'en' => 'Sample industrial customer', 'ar' => 'عميل صناعي نموذجي'], 'summary' => ['fa' => 'یک نمونه نمایشی از انتخاب راهکار مناسب جابه‌جایی بار.', 'en' => 'A demonstration of selecting an appropriate lifting solution.', 'ar' => 'عرض توضيحي لاختيار حل رفع مناسب.'], 'body' => ['fa' => 'پس از بررسی جریان مواد و ایمنی، تجهیزات متناسب با فضای کارگاه پیشنهاد شد. این داستان مشتری در حالت دمو نمایش داده می‌شود.', 'en' => 'After reviewing material flow and safety, equipment suitable for the workshop was proposed. This story appears in demo mode.', 'ar' => 'بعد مراجعة تدفق المواد والسلامة، اقترحت معدات مناسبة للورشة. تظهر هذه القصة في وضع العرض.'], 'is_published' => true, 'is_demo' => true, 'published_at' => now()]);
        $study->products()->sync([$products->last()->id]);

        if (app()->environment(['local', 'testing'])) {
            User::query()->updateOrCreate(['email' => 'admin@souri.test'], ['name' => 'Souri Local Admin', 'password' => env('LOCAL_DEMO_ADMIN_PASSWORD', 'local-demo-password-change-me'), 'role' => 'super_admin']);
        }
    }
}
