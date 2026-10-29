<div class="mx-auto max-w-5xl px-4 py-10 sm:px-6 lg:px-8">
    <a href="{{ route('cranes.index') }}" wire:navigate class="text-sm font-semibold text-sky-700 hover:text-sky-800">بازگشت به فهرست جرثقیل‌ها</a>

    <article class="mt-5 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="grid lg:grid-cols-2">
            <div class="flex aspect-[4/3] items-center justify-center bg-slate-100 text-slate-400">
                @if ($crane->images->isNotEmpty())
                    <img src="{{ Storage::url($crane->images->first()->path) }}" alt="{{ $crane->images->first()->alt_text ?: $crane->model }}" class="h-full w-full object-cover">
                @else
                    تصویر جرثقیل
                @endif
            </div>

            <div class="p-6 sm:p-8">
                <p class="text-sm font-semibold text-sky-700">{{ $crane->craneType->name }}</p>
                <h1 class="mt-2 text-3xl font-bold text-slate-950">{{ $crane->manufacturer }} {{ $crane->model }}</h1>
                <p class="mt-3 text-slate-600">{{ $crane->description ?: 'توضیحی برای این جرثقیل ثبت نشده است.' }}</p>

                <dl class="mt-6 grid gap-4 sm:grid-cols-2">
                    <div class="rounded-lg bg-slate-50 p-4">
                        <dt class="text-sm text-slate-500">ظرفیت اسمی</dt>
                        <dd class="mt-1 text-lg font-bold text-slate-900">{{ number_format($crane->capacity_kg) }} کیلوگرم</dd>
                    </div>
                    <div class="rounded-lg bg-slate-50 p-4">
                        <dt class="text-sm text-slate-500">طول بوم</dt>
                        <dd class="mt-1 text-lg font-bold text-slate-900">
                            {{ $crane->boom_length_mm ? number_format($crane->boom_length_mm / 1000, 1) . ' متر' : 'ثبت نشده' }}
                        </dd>
                    </div>
                    <div class="rounded-lg bg-slate-50 p-4">
                        <dt class="text-sm text-slate-500">سال ساخت</dt>
                        <dd class="mt-1 text-lg font-bold text-slate-900">{{ $crane->manufacture_year ?: 'ثبت نشده' }}</dd>
                    </div>
                    <div class="rounded-lg bg-slate-50 p-4">
                        <dt class="text-sm text-slate-500">ارائه‌دهنده</dt>
                        <dd class="mt-1 text-lg font-bold text-slate-900">{{ $crane->organization->name }}</dd>
                    </div>
                </dl>

                <section class="mt-6" aria-labelledby="service-areas-heading">
                    <h2 id="service-areas-heading" class="font-bold text-slate-900">محدودهٔ سرویس</h2>
                    <ul class="mt-2 flex flex-wrap gap-2">
                        @forelse ($crane->serviceAreas as $area)
                            <li class="rounded-full bg-sky-50 px-3 py-1 text-sm text-sky-800">
                                {{ $area->city?->name ?? $area->province->name }}
                            </li>
                        @empty
                            <li class="text-sm text-slate-600">محدودهٔ سرویس ثبت نشده است.</li>
                        @endforelse
                    </ul>
                </section>
            </div>
        </div>
    </article>
</div>
