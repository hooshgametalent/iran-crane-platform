<div class="mx-auto max-w-3xl px-4 py-10 sm:px-6 lg:px-8">
    <a href="{{ route('owner.dashboard') }}" wire:navigate class="text-sm font-semibold text-sky-700 hover:text-sky-800">بازگشت به پنل مالک</a>
    <section class="mt-5 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8" aria-labelledby="quote-heading">
        <p class="text-sm font-semibold text-sky-700">درخواست باز</p>
        <h1 id="quote-heading" class="mt-2 text-2xl font-bold text-slate-950">ارسال پیشنهاد قیمت</h1>
        <dl class="mt-5 grid gap-3 rounded-xl bg-slate-50 p-4 text-sm text-slate-700 sm:grid-cols-2">
            <div><dt class="text-slate-500">عنوان</dt><dd class="mt-1 font-semibold text-slate-900">{{ $rentalRequest->title }}</dd></div>
            <div><dt class="text-slate-500">محل پروژه</dt><dd class="mt-1 font-semibold text-slate-900">{{ $rentalRequest->city->name }}، {{ $rentalRequest->city->province->name }}</dd></div>
            <div><dt class="text-slate-500">حداقل ظرفیت</dt><dd class="mt-1 font-semibold text-slate-900">{{ $rentalRequest->required_capacity_kg ? number_format($rentalRequest->required_capacity_kg) . ' کیلوگرم' : 'تعیین نشده' }}</dd></div>
            <div><dt class="text-slate-500">زمان شروع</dt><dd class="mt-1 font-semibold text-slate-900">{{ \App\Support\JalaliDate::dateTime($rentalRequest->starts_at) }}</dd></div>
        </dl>

        @if ($eligibleCranes->isEmpty())
            <p class="mt-6 rounded-lg border border-amber-200 bg-amber-50 p-4 text-sm text-amber-900">هیچ جرثقیل فعال و سازگاری از مجموعه‌های شما برای این درخواست پیدا نشد.</p>
        @else
            <form wire:submit="submit" class="mt-6 grid gap-5">
                <label class="grid gap-2 text-sm font-medium text-slate-700">جرثقیل پیشنهادی
                    <select wire:model="craneId" required class="rounded-lg border-slate-300 bg-white text-slate-900 focus:border-sky-600 focus:ring-sky-600">
                        <option value="">انتخاب جرثقیل</option>
                        @foreach ($eligibleCranes as $crane)
                            <option value="{{ $crane->id }}">{{ $crane->manufacturer }} {{ $crane->model }} · {{ number_format($crane->capacity_kg) }} کیلوگرم</option>
                        @endforeach
                    </select>
                    @error('craneId') <span class="text-sm text-red-700">{{ $message }}</span> @enderror
                </label>
                <div class="grid gap-5 md:grid-cols-2">
                    <label class="grid gap-2 text-sm font-medium text-slate-700">مبلغ ({{ $currency }})
                        <input wire:model="amount" type="number" min="1" required class="rounded-lg border-slate-300 text-slate-900 focus:border-sky-600 focus:ring-sky-600">
                        @error('amount') <span class="text-sm text-red-700">{{ $message }}</span> @enderror
                    </label>
                    <label class="grid gap-2 text-sm font-medium text-slate-700">واحد قیمت‌گذاری
                        <select wire:model="billingUnit" class="rounded-lg border-slate-300 bg-white text-slate-900 focus:border-sky-600 focus:ring-sky-600"><option value="hour">ساعتی</option><option value="day">روزانه</option><option value="project">پروژه‌ای</option></select>
                    </label>
                </div>
                <label class="grid gap-2 text-sm font-medium text-slate-700">هزینهٔ حمل (اختیاری، {{ $currency }})
                    <input wire:model="transportFee" type="number" min="0" class="rounded-lg border-slate-300 text-slate-900 focus:border-sky-600 focus:ring-sky-600">
                    @error('transportFee') <span class="text-sm text-red-700">{{ $message }}</span> @enderror
                </label>
                <label class="grid gap-2 text-sm font-medium text-slate-700">تاریخ انقضا
                    <input wire:model="expiresAt" type="text" required placeholder="۱۴۰۵/۰۷/۱۵ ۰۹:۳۰" class="rounded-lg border-slate-300 text-slate-900 focus:border-sky-600 focus:ring-sky-600">
                    @error('expiresAt') <span class="text-sm text-red-700">{{ $message }}</span> @enderror
                </label>
                <label class="grid gap-2 text-sm font-medium text-slate-700">شرایط پیشنهاد
                    <textarea wire:model="terms" rows="4" maxlength="5000" class="rounded-lg border-slate-300 text-slate-900 focus:border-sky-600 focus:ring-sky-600"></textarea>
                </label>
                <button type="submit" class="inline-flex w-fit rounded-lg bg-sky-700 px-5 py-2.5 text-sm font-semibold text-white hover:bg-sky-800 focus:outline-none focus:ring-2 focus:ring-sky-700 focus:ring-offset-2" wire:loading.attr="disabled"><span wire:loading.remove>ارسال پیشنهاد</span><span wire:loading>در حال ارسال…</span></button>
            </form>
        @endif
    </section>
</div>
