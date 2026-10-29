<div class="mx-auto max-w-6xl px-4 py-10 sm:px-6 lg:px-8">
    <section class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <p class="text-sm font-semibold text-sky-700">کارفرما</p>
            <h1 class="mt-2 text-3xl font-bold text-slate-950">درخواست‌های من</h1>
            <p class="mt-2 text-slate-600">وضعیت درخواست‌های ثبت‌شدهٔ اجاره را پیگیری کنید.</p>
        </div>
        <a href="{{ route('requests.create') }}" wire:navigate class="rounded-lg bg-sky-700 px-4 py-2.5 text-sm font-semibold text-white hover:bg-sky-800 focus:outline-none focus:ring-2 focus:ring-sky-700 focus:ring-offset-2">ثبت درخواست جدید</a>
    </section>

    <section class="mt-8 overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm" aria-label="فهرست درخواست‌ها">
        @if ($requests->isEmpty())
            <p class="p-6 text-slate-600">هنوز درخواستی ثبت نکرده‌اید.</p>
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
                        @foreach ($requests as $request)
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

            <div class="border-t border-slate-200 px-5 py-4">
                {{ $requests->links() }}
            </div>
        @endif
    </section>
</div>
