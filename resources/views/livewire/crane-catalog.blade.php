<div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
    <section class="mb-8">
        <p class="text-sm font-semibold text-sky-700">بازار اجاره جرثقیل</p>
        <h1 class="mt-2 text-3xl font-bold tracking-tight text-slate-950 sm:text-4xl">جرثقیل مناسب پروژه‌تان را پیدا کنید</h1>
        <p class="mt-3 max-w-2xl text-slate-600">بر اساس شهر، نوع جرثقیل و ظرفیت موردنیاز جست‌وجو کنید.</p>
    </section>

    <section class="mb-8 rounded-xl border border-slate-200 bg-white p-4 shadow-sm" aria-label="فیلتر جرثقیل‌ها">
        <div class="grid gap-4 md:grid-cols-3">
            <label class="grid gap-2 text-sm font-medium text-slate-700">
                شهر پروژه
                <select wire:model.live="cityId" class="rounded-lg border-slate-300 bg-white text-slate-900 focus:border-sky-600 focus:ring-sky-600">
                    <option value="">همهٔ شهرها</option>
                    @foreach ($cities as $city)
                        <option value="{{ $city->id }}">{{ $city->name }}، {{ $city->province->name }}</option>
                    @endforeach
                </select>
            </label>

            <label class="grid gap-2 text-sm font-medium text-slate-700">
                نوع جرثقیل
                <select wire:model.live="craneTypeId" class="rounded-lg border-slate-300 bg-white text-slate-900 focus:border-sky-600 focus:ring-sky-600">
                    <option value="">همهٔ نوع‌ها</option>
                    @foreach ($craneTypes as $craneType)
                        <option value="{{ $craneType->id }}">{{ $craneType->name }}</option>
                    @endforeach
                </select>
            </label>

            <label class="grid gap-2 text-sm font-medium text-slate-700">
                حداقل ظرفیت، کیلوگرم
                <input wire:model.live.debounce.400ms="minimumCapacity" type="number" min="0" inputmode="numeric" placeholder="مثلاً 50000" class="rounded-lg border-slate-300 text-slate-900 focus:border-sky-600 focus:ring-sky-600">
            </label>
        </div>
    </section>

    <section aria-labelledby="catalogue-heading">
        <div class="mb-4 flex items-center justify-between gap-4">
            <h2 id="catalogue-heading" class="text-xl font-bold text-slate-900">جرثقیل‌های موجود</h2>
            <span class="text-sm text-slate-600">{{ $cranes->total() }} مورد</span>
        </div>

        <div wire:loading.flex class="mb-4 items-center gap-2 text-sm text-sky-700" role="status">
            در حال به‌روزرسانی فهرست…
        </div>

        @if ($cranes->isEmpty())
            <div class="rounded-xl border border-dashed border-slate-300 bg-white p-8 text-center text-slate-600">
                با این فیلترها جرثقیلی پیدا نشد. فیلترها را تغییر دهید.
            </div>
        @else
            <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($cranes as $crane)
                    <article class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
                        <div class="flex aspect-[16/9] items-center justify-center bg-slate-100 text-slate-400">
                            @if ($crane->images->isNotEmpty())
                                <img src="{{ Storage::url($crane->images->first()->path) }}" alt="{{ $crane->images->first()->alt_text ?: $crane->model }}" class="h-full w-full object-cover">
                            @else
                                تصویر جرثقیل
                            @endif
                        </div>

                        <div class="p-5">
                            <p class="text-sm font-medium text-sky-700">{{ $crane->craneType->name }}</p>
                            <h3 class="mt-1 text-lg font-bold text-slate-950">{{ $crane->manufacturer }} {{ $crane->model }}</h3>
                            <dl class="mt-4 grid grid-cols-2 gap-3 text-sm">
                                <div>
                                    <dt class="text-slate-500">ظرفیت</dt>
                                    <dd class="mt-1 font-semibold text-slate-800">{{ number_format($crane->capacity_kg) }} کیلوگرم</dd>
                                </div>
                                <div>
                                    <dt class="text-slate-500">مالک</dt>
                                    <dd class="mt-1 font-semibold text-slate-800">{{ $crane->organization->name }}</dd>
                                </div>
                            </dl>

                            <a href="{{ route('cranes.show', $crane) }}" wire:navigate class="mt-5 inline-flex rounded-lg bg-sky-700 px-4 py-2 text-sm font-semibold text-white hover:bg-sky-800 focus:outline-none focus:ring-2 focus:ring-sky-700 focus:ring-offset-2">
                                مشاهدهٔ جزئیات
                            </a>
                        </div>
                    </article>
                @endforeach
            </div>

            <div class="mt-8">
                {{ $cranes->links() }}
            </div>
        @endif
    </section>
</div>
