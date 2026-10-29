<div class="mx-auto max-w-3xl px-4 py-10 sm:px-6 lg:px-8">
    <a href="{{ route('admin.dashboard') }}" wire:navigate class="text-sm font-semibold text-sky-700 hover:text-sky-800">بازگشت به داشبورد</a>

    <section class="mt-5 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8" aria-labelledby="settings-heading">
        <p class="text-sm font-semibold text-sky-700">تنظیمات عمومی</p>
        <h1 id="settings-heading" class="mt-2 text-2xl font-bold text-slate-950">واحد پایهٔ مبالغ</h1>
        <p class="mt-2 text-sm leading-6 text-slate-600">این مقدار فقط واحد پیش‌فرض قیمت‌گذاری‌های جدید را تعیین می‌کند. مبلغ‌ها و واحد پول رکوردهای قبلی به‌صورت snapshot حفظ می‌شوند.</p>

        <form wire:submit="save" class="mt-6 grid gap-5">
            <label class="grid max-w-xs gap-2 text-sm font-medium text-slate-700">
                کد سه‌حرفی واحد پول
                <input wire:model="defaultCurrency" type="text" maxlength="3" dir="ltr" autocomplete="off" class="rounded-lg border-slate-300 text-left uppercase text-slate-900 focus:border-sky-600 focus:ring-sky-600">
                @error('defaultCurrency')
                    <span class="text-sm text-red-700">{{ $message }}</span>
                @enderror
            </label>

            @if ($saved)
                <p class="text-sm font-medium text-emerald-700" role="status">تنظیمات ذخیره شد.</p>
            @endif

            <button type="submit" class="inline-flex w-fit rounded-lg bg-sky-700 px-4 py-2.5 text-sm font-semibold text-white hover:bg-sky-800 focus:outline-none focus:ring-2 focus:ring-sky-700 focus:ring-offset-2" wire:loading.attr="disabled">
                <span wire:loading.remove>ذخیرهٔ تنظیمات</span>
                <span wire:loading>در حال ذخیره…</span>
            </button>
        </form>
    </section>
</div>
