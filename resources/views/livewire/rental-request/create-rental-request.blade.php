<div class="mx-auto max-w-3xl px-4 py-10 sm:px-6 lg:px-8">
    <a href="{{ route('requests.mine') }}" wire:navigate class="text-sm font-semibold text-sky-700 hover:text-sky-800">مشاهدهٔ درخواست‌های من</a>

    <section class="mt-5 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8" aria-labelledby="request-heading">
        <p class="text-sm font-semibold text-sky-700">کارفرما</p>
        <h1 id="request-heading" class="mt-2 text-2xl font-bold text-slate-950">ثبت درخواست اجارهٔ جرثقیل</h1>
        <p class="mt-2 text-sm leading-6 text-slate-600">تاریخ را به تقویم شمسی و زمان تهران وارد کنید. برای نمونه: ۱۴۰۵/۰۷/۱۵ ۰۹:۳۰</p>

        <form wire:submit="submit" class="mt-6 grid gap-5">
            <label class="grid gap-2 text-sm font-medium text-slate-700">
                عنوان درخواست
                <input wire:model="title" type="text" maxlength="255" required placeholder="مثلاً اجاره جرثقیل ۵۰ تن برای پروژه تهران" class="rounded-lg border-slate-300 text-slate-900 focus:border-sky-600 focus:ring-sky-600">
                @error('title') <span class="text-sm text-red-700">{{ $message }}</span> @enderror
            </label>

            <div class="grid gap-5 md:grid-cols-2">
                <label class="grid gap-2 text-sm font-medium text-slate-700">
                    شهر پروژه
                    <select wire:model="cityId" required class="rounded-lg border-slate-300 bg-white text-slate-900 focus:border-sky-600 focus:ring-sky-600">
                        <option value="">انتخاب شهر</option>
                        @foreach ($cities as $city)
                            <option value="{{ $city->id }}">{{ $city->name }}، {{ $city->province->name }}</option>
                        @endforeach
                    </select>
                    @error('cityId') <span class="text-sm text-red-700">{{ $message }}</span> @enderror
                </label>

                <label class="grid gap-2 text-sm font-medium text-slate-700">
                    نوع جرثقیل
                    <select wire:model="craneTypeId" class="rounded-lg border-slate-300 bg-white text-slate-900 focus:border-sky-600 focus:ring-sky-600">
                        <option value="">تفاوتی ندارد</option>
                        @foreach ($craneTypes as $craneType)
                            <option value="{{ $craneType->id }}">{{ $craneType->name }}</option>
                        @endforeach
                    </select>
                    @error('craneTypeId') <span class="text-sm text-red-700">{{ $message }}</span> @enderror
                </label>
            </div>

            <div class="grid gap-5 md:grid-cols-3">
                <label class="grid gap-2 text-sm font-medium text-slate-700">
                    حداقل ظرفیت، کیلوگرم
                    <input wire:model="requiredCapacityKg" type="number" min="1" inputmode="numeric" placeholder="50000" class="rounded-lg border-slate-300 text-slate-900 focus:border-sky-600 focus:ring-sky-600">
                    @error('requiredCapacityKg') <span class="text-sm text-red-700">{{ $message }}</span> @enderror
                </label>

                <label class="grid gap-2 text-sm font-medium text-slate-700 md:col-span-2">
                    شروع کار
                    <input wire:model="startsAt" type="text" inputmode="text" required placeholder="۱۴۰۵/۰۷/۱۵ ۰۹:۳۰" class="rounded-lg border-slate-300 text-slate-900 focus:border-sky-600 focus:ring-sky-600">
                    @error('startsAt') <span class="text-sm text-red-700">{{ $message }}</span> @enderror
                </label>
            </div>

            <label class="grid gap-2 text-sm font-medium text-slate-700">
                پایان کار
                <input wire:model="endsAt" type="text" inputmode="text" required placeholder="۱۴۰۵/۰۷/۱۷ ۱۸:۰۰" class="rounded-lg border-slate-300 text-slate-900 focus:border-sky-600 focus:ring-sky-600">
                @error('endsAt') <span class="text-sm text-red-700">{{ $message }}</span> @enderror
            </label>

            <label class="grid gap-2 text-sm font-medium text-slate-700">
                توضیحات
                <textarea wire:model="description" rows="5" maxlength="5000" placeholder="جزئیات پروژه، محدودیت محل و نیازهای اجرایی را بنویسید." class="rounded-lg border-slate-300 text-slate-900 focus:border-sky-600 focus:ring-sky-600"></textarea>
                @error('description') <span class="text-sm text-red-700">{{ $message }}</span> @enderror
            </label>

            <button type="submit" class="inline-flex w-fit rounded-lg bg-sky-700 px-5 py-2.5 text-sm font-semibold text-white hover:bg-sky-800 focus:outline-none focus:ring-2 focus:ring-sky-700 focus:ring-offset-2" wire:loading.attr="disabled">
                <span wire:loading.remove>ثبت درخواست</span>
                <span wire:loading>در حال ثبت…</span>
            </button>
        </form>
    </section>
</div>
