<div class="mx-auto flex min-h-[calc(100vh-73px)] max-w-md items-center px-4 py-10">
    <section class="w-full rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8" aria-labelledby="register-heading">
        <p class="text-sm font-semibold text-sky-700">شروع همکاری</p>
        <h1 id="register-heading" class="mt-2 text-2xl font-bold text-slate-950">ایجاد حساب کاربری</h1>
        <p class="mt-2 text-sm text-slate-600">با ایجاد حساب، نقش کارفرما به شما اختصاص داده می‌شود و می‌توانید درخواست اجاره ثبت کنید.</p>

        <form wire:submit="register" class="mt-6 grid gap-5">
            <label class="grid gap-2 text-sm font-medium text-slate-700">
                نام و نام خانوادگی
                <input wire:model="name" type="text" autocomplete="name" required class="rounded-lg border-slate-300 text-slate-900 focus:border-sky-600 focus:ring-sky-600">
                @error('name') <span class="text-sm text-red-700">{{ $message }}</span> @enderror
            </label>

            <label class="grid gap-2 text-sm font-medium text-slate-700">
                ایمیل
                <input wire:model="email" type="email" autocomplete="email" dir="ltr" required class="rounded-lg border-slate-300 text-left text-slate-900 focus:border-sky-600 focus:ring-sky-600">
                @error('email') <span class="text-sm text-red-700">{{ $message }}</span> @enderror
            </label>

            <label class="grid gap-2 text-sm font-medium text-slate-700">
                رمز عبور
                <input wire:model="password" type="password" autocomplete="new-password" required class="rounded-lg border-slate-300 text-left text-slate-900 focus:border-sky-600 focus:ring-sky-600">
                @error('password') <span class="text-sm text-red-700">{{ $message }}</span> @enderror
            </label>

            <label class="grid gap-2 text-sm font-medium text-slate-700">
                تکرار رمز عبور
                <input wire:model="passwordConfirmation" type="password" autocomplete="new-password" required class="rounded-lg border-slate-300 text-left text-slate-900 focus:border-sky-600 focus:ring-sky-600">
                @error('passwordConfirmation') <span class="text-sm text-red-700">{{ $message }}</span> @enderror
            </label>

            <button type="submit" class="inline-flex justify-center rounded-lg bg-sky-700 px-4 py-2.5 text-sm font-semibold text-white hover:bg-sky-800 focus:outline-none focus:ring-2 focus:ring-sky-700 focus:ring-offset-2" wire:loading.attr="disabled">
                <span wire:loading.remove>ایجاد حساب</span>
                <span wire:loading>در حال ایجاد حساب…</span>
            </button>
        </form>

        <p class="mt-6 text-center text-sm text-slate-600">
            حساب دارید؟
            <a href="{{ route('login') }}" wire:navigate class="font-semibold text-sky-700 hover:text-sky-800">وارد شوید</a>
        </p>
    </section>
</div>
