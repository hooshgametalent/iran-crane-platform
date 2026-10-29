<div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
    <section class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <p class="text-sm font-semibold text-sky-700">مالک جرثقیل</p>
            <h1 class="mt-2 text-3xl font-bold text-slate-950">پنل مالک</h1>
            <p class="mt-2 text-slate-600">جرثقیل‌های خود را مدیریت و برای درخواست‌های سازگار پیشنهاد ارسال کنید.</p>
        </div>
        <a href="{{ route('owner.cranes.create') }}" wire:navigate class="rounded-lg bg-sky-700 px-4 py-2.5 text-sm font-semibold text-white hover:bg-sky-800 focus:outline-none focus:ring-2 focus:ring-sky-700 focus:ring-offset-2">ثبت جرثقیل</a>
    </section>

    <section class="mt-8" aria-labelledby="owner-cranes-heading">
        <h2 id="owner-cranes-heading" class="text-xl font-bold text-slate-950">جرثقیل‌های من</h2>
        @if ($cranes->isEmpty())
            <p class="mt-4 rounded-xl border border-dashed border-slate-300 bg-white p-6 text-slate-600">هنوز جرثقیلی ثبت نکرده‌اید.</p>
        @else
            <div class="mt-4 grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                @foreach ($cranes as $crane)
                    <article class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                        <p class="text-sm font-semibold text-sky-700">{{ $crane->craneType->name }}</p>
                        <h3 class="mt-1 text-lg font-bold text-slate-950">{{ $crane->manufacturer }} {{ $crane->model }}</h3>
                        <p class="mt-2 text-sm text-slate-600">{{ number_format($crane->capacity_kg) }} کیلوگرم · {{ $crane->status }}</p>
                        <a href="{{ route('owner.cranes.edit', $crane) }}" wire:navigate class="mt-4 inline-flex text-sm font-semibold text-sky-700 hover:text-sky-800">ویرایش</a>
                    </article>
                @endforeach
            </div>
        @endif
    </section>

    <section class="mt-10" aria-labelledby="matching-requests-heading">
        <h2 id="matching-requests-heading" class="text-xl font-bold text-slate-950">درخواست‌های سازگار</h2>
        <p class="mt-2 text-sm text-slate-600">حداکثر ۳۰ درخواست باز اخیر بررسی شده‌اند.</p>

        @if ($openRequests->isEmpty())
            <p class="mt-4 rounded-xl border border-dashed border-slate-300 bg-white p-6 text-slate-600">درخواست سازگاری برای جرثقیل‌های شما پیدا نشد.</p>
        @else
            <div class="mt-4 grid gap-4 lg:grid-cols-2">
                @foreach ($openRequests as $request)
                    <article class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                        <h3 class="font-bold text-slate-950">{{ $request->title }}</h3>
                        <p class="mt-2 text-sm text-slate-600">{{ $request->city->name }}، {{ $request->city->province->name }}</p>
                        <p class="mt-1 text-sm text-slate-600">{{ $request->required_capacity_kg ? number_format($request->required_capacity_kg) . ' کیلوگرم' : 'ظرفیت مشخص نشده' }}</p>
                        <p class="mt-1 text-sm text-slate-600">شروع: {{ \App\Support\JalaliDate::dateTime($request->starts_at) }}</p>
                        <a href="{{ route('owner.quotes.create', $request) }}" wire:navigate class="mt-4 inline-flex rounded-lg bg-sky-700 px-4 py-2 text-sm font-semibold text-white hover:bg-sky-800 focus:outline-none focus:ring-2 focus:ring-sky-700 focus:ring-offset-2">ارسال پیشنهاد</a>
                    </article>
                @endforeach
            </div>
        @endif
    </section>
</div>
