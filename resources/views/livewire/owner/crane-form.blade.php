<div class="mx-auto max-w-3xl px-4 py-10 sm:px-6 lg:px-8">
    <a href="{{ route('owner.dashboard') }}" wire:navigate class="text-sm font-semibold text-sky-700 hover:text-sky-800">بازگشت به پنل مالک</a>
    <section class="mt-5 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8" aria-labelledby="crane-form-heading">
        <h1 id="crane-form-heading" class="text-2xl font-bold text-slate-950">{{ $crane === null ? 'ثبت جرثقیل' : 'ویرایش جرثقیل' }}</h1>
        <form wire:submit="save" class="mt-6 grid gap-5">
            <div class="grid gap-5 md:grid-cols-2">
                <label class="grid gap-2 text-sm font-medium text-slate-700">مالک
                    <select wire:model="organizationId" required class="rounded-lg border-slate-300 bg-white text-slate-900 focus:border-sky-600 focus:ring-sky-600">
                        <option value="">انتخاب مجموعه</option>
                        @foreach ($organizations as $organization)
                            <option value="{{ $organization->id }}">{{ $organization->name }}</option>
                        @endforeach
                    </select>
                    @error('organizationId') <span class="text-sm text-red-700">{{ $message }}</span> @enderror
                </label>
                <label class="grid gap-2 text-sm font-medium text-slate-700">نوع جرثقیل
                    <select wire:model="craneTypeId" required class="rounded-lg border-slate-300 bg-white text-slate-900 focus:border-sky-600 focus:ring-sky-600">
                        <option value="">انتخاب نوع</option>
                        @foreach ($craneTypes as $craneType)
                            <option value="{{ $craneType->id }}">{{ $craneType->name }}</option>
                        @endforeach
                    </select>
                    @error('craneTypeId') <span class="text-sm text-red-700">{{ $message }}</span> @enderror
                </label>
            </div>
            <div class="grid gap-5 md:grid-cols-2">
                <label class="grid gap-2 text-sm font-medium text-slate-700">شناسهٔ داخلی
                    <input wire:model="ownerReference" type="text" required maxlength="255" class="rounded-lg border-slate-300 text-slate-900 focus:border-sky-600 focus:ring-sky-600">
                    @error('ownerReference') <span class="text-sm text-red-700">{{ $message }}</span> @enderror
                </label>
                <label class="grid gap-2 text-sm font-medium text-slate-700">ظرفیت، کیلوگرم
                    <input wire:model="capacityKg" type="number" min="1" required class="rounded-lg border-slate-300 text-slate-900 focus:border-sky-600 focus:ring-sky-600">
                    @error('capacityKg') <span class="text-sm text-red-700">{{ $message }}</span> @enderror
                </label>
            </div>
            <div class="grid gap-5 md:grid-cols-3">
                <label class="grid gap-2 text-sm font-medium text-slate-700">سازنده<input wire:model="manufacturer" type="text" class="rounded-lg border-slate-300 text-slate-900 focus:border-sky-600 focus:ring-sky-600"></label>
                <label class="grid gap-2 text-sm font-medium text-slate-700">مدل<input wire:model="model" type="text" class="rounded-lg border-slate-300 text-slate-900 focus:border-sky-600 focus:ring-sky-600"></label>
                <label class="grid gap-2 text-sm font-medium text-slate-700">سال ساخت<input wire:model="manufactureYear" type="number" min="1900" class="rounded-lg border-slate-300 text-slate-900 focus:border-sky-600 focus:ring-sky-600"></label>
            </div>
            <label class="grid gap-2 text-sm font-medium text-slate-700">طول بوم، میلی‌متر<input wire:model="boomLengthMm" type="number" min="1" class="rounded-lg border-slate-300 text-slate-900 focus:border-sky-600 focus:ring-sky-600"></label>
            <fieldset class="grid gap-2 text-sm font-medium text-slate-700"><legend>شهرهای قابل سرویس</legend><div class="grid gap-2 sm:grid-cols-2">@foreach ($cities as $city)<label class="flex items-center gap-2 rounded-lg border border-slate-200 px-3 py-2"><input wire:model="serviceCityIds" type="checkbox" value="{{ $city->id }}" class="rounded border-slate-300 text-sky-700 focus:ring-sky-600">{{ $city->name }}، {{ $city->province->name }}</label>@endforeach</div>@error('serviceCityIds') <span class="text-sm text-red-700">{{ $message }}</span> @enderror</fieldset>
            <label class="grid gap-2 text-sm font-medium text-slate-700">توضیحات<textarea wire:model="description" rows="4" class="rounded-lg border-slate-300 text-slate-900 focus:border-sky-600 focus:ring-sky-600"></textarea></label>
            <button type="submit" class="inline-flex w-fit rounded-lg bg-sky-700 px-5 py-2.5 text-sm font-semibold text-white hover:bg-sky-800 focus:outline-none focus:ring-2 focus:ring-sky-700 focus:ring-offset-2" wire:loading.attr="disabled"><span wire:loading.remove>ذخیرهٔ جرثقیل</span><span wire:loading>در حال ذخیره…</span></button>
        </form>
    </section>
</div>
