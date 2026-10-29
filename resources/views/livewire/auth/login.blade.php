<div class="mx-auto flex min-h-[calc(100vh-73px)] max-w-md items-center px-4 py-10">
    <section class="w-full rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8" aria-labelledby="login-heading">
        <p class="text-sm font-semibold text-sky-700">دسترسی به سامانه</p>
        <h1 id="login-heading" class="mt-2 text-2xl font-bold text-slate-950">ورود</h1>
        <p class="mt-2 text-sm text-slate-600">برای مدیریت حساب و پنل کاربری وارد شوید.</p>

        <form wire:submit="authenticate" class="mt-6 grid gap-5">
            <label class="grid gap-2 text-sm font-medium text-slate-700">
                ایمیل
                <input wire:model="email" type="email" autocomplete="email" dir="ltr" required class="rounded-lg border-slate-300 text-left text-slate-900 focus:border-sky-600 focus:ring-sky-600">
                @error('email')
                    <span class="text-sm text-red-700">{{ $message }}</span>
                @enderror
            </label>

            <label class="grid gap-2 text-sm font-medium text-slate-700">
                رمز عبور
                <input wire:model="password" type="password" autocomplete="current-password" required class="rounded-lg border-slate-300 text-left text-slate-900 focus:border-sky-600 focus:ring-sky-600">
                @error('password')
                    <span class="text-sm text-red-700">{{ $message }}</span>
                @enderror
            </label>

            <label class="flex items-center gap-2 text-sm text-slate-700">
                <input wire:model="remember" type="checkbox" class="rounded border-slate-300 text-sky-700 focus:ring-sky-600">
                مرا به خاطر بسپار
            </label>

            <button type="submit" class="inline-flex justify-center rounded-lg bg-sky-700 px-4 py-2.5 text-sm font-semibold text-white hover:bg-sky-800 focus:outline-none focus:ring-2 focus:ring-sky-700 focus:ring-offset-2" wire:loading.attr="disabled">
                <span wire:loading.remove>ورود به سامانه</span>
                <span wire:loading>در حال ورود…</span>
            </button>
        </form>
    </section>
</div>
