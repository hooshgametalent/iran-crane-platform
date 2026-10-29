<div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
    <section class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <p class="text-sm font-semibold text-sky-700">مدیریت پلتفرم</p>
            <h1 class="mt-2 text-3xl font-bold text-slate-950">داشبورد ادمین</h1>
            <p class="mt-2 text-slate-600">نمای کلی داده‌های جاری بازار اجارهٔ جرثقیل.</p>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <a href="{{ route('admin.settings') }}" wire:navigate class="rounded-lg bg-sky-700 px-4 py-2 text-sm font-semibold text-white hover:bg-sky-800 focus:outline-none focus:ring-2 focus:ring-sky-700 focus:ring-offset-2">تنظیمات</a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100 focus:outline-none focus:ring-2 focus:ring-sky-700 focus:ring-offset-2">خروج</button>
            </form>
        </div>
    </section>

    <section class="mt-8 grid gap-4 sm:grid-cols-2 xl:grid-cols-4" aria-label="آمار پلتفرم">
        <article class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm text-slate-600">جرثقیل فعال</p>
            <p class="mt-2 text-3xl font-bold text-slate-950">{{ number_format($activeCraneCount) }}</p>
        </article>
        <article class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm text-slate-600">درخواست باز</p>
            <p class="mt-2 text-3xl font-bold text-slate-950">{{ number_format($openRequestCount) }}</p>
        </article>
        <article class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm text-slate-600">پیشنهاد در انتظار</p>
            <p class="mt-2 text-3xl font-bold text-slate-950">{{ number_format($pendingQuoteCount) }}</p>
        </article>
        <article class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm text-slate-600">رزرو تأییدشده</p>
            <p class="mt-2 text-3xl font-bold text-slate-950">{{ number_format($confirmedBookingCount) }}</p>
        </article>
    </section>

    <section class="mt-8 overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm" aria-labelledby="latest-requests-heading">
        <div class="border-b border-slate-200 px-5 py-4">
            <h2 id="latest-requests-heading" class="text-lg font-bold text-slate-950">آخرین درخواست‌ها</h2>
        </div>

        @if ($latestRequests->isEmpty())
            <p class="p-5 text-slate-600">درخواستی ثبت نشده است.</p>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-right text-sm">
                    <thead class="bg-slate-50 text-slate-600">
                        <tr>
                            <th scope="col" class="px-5 py-3 font-semibold">عنوان</th>
                            <th scope="col" class="px-5 py-3 font-semibold">شهر</th>
                            <th scope="col" class="px-5 py-3 font-semibold">شروع</th>
                            <th scope="col" class="px-5 py-3 font-semibold">وضعیت</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-800">
                        @foreach ($latestRequests as $request)
                            <tr>
                                <td class="px-5 py-4 font-medium">{{ $request->title }}</td>
                                <td class="px-5 py-4">{{ $request->city->name }}، {{ $request->city->province->name }}</td>
                                <td class="px-5 py-4">{{ \App\Support\JalaliDate::dateTime($request->starts_at) }}</td>
                                <td class="px-5 py-4">{{ $request->status }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>
</div>
