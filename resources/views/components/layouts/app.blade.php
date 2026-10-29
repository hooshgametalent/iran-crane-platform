<!DOCTYPE html>
<html lang="fa" dir="rtl">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ $title ?? config('app.name') }}</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
    </head>
    <body class="min-h-screen bg-slate-50 text-slate-900 antialiased">
        <header class="border-b border-slate-200 bg-white">
            <nav class="mx-auto flex max-w-7xl items-center justify-between px-4 py-4 sm:px-6 lg:px-8" aria-label="ناوبری اصلی">
                <a href="{{ route('cranes.index') }}" class="text-lg font-bold text-slate-900">
                    پلتفرم جرثقیل ایران
                </a>

                <div class="flex items-center gap-4">
                    <a href="{{ route('cranes.index') }}" wire:navigate class="text-sm font-medium text-slate-700 hover:text-sky-700">
                        جرثقیل‌ها
                    </a>

                    @guest
                        <div class="flex items-center gap-3">
                            <a href="{{ route('login') }}" wire:navigate class="text-sm font-semibold text-sky-700 hover:text-sky-800">
                                ورود
                            </a>
                            <a href="{{ route('register') }}" wire:navigate class="rounded-lg bg-sky-700 px-3 py-2 text-sm font-semibold text-white hover:bg-sky-800 focus:outline-none focus:ring-2 focus:ring-sky-700 focus:ring-offset-2">
                                ثبت‌نام
                            </a>
                        </div>
                    @else
                        <div class="flex items-center gap-4">
                            @if (auth()->user()->roles()->where('name', 'customer')->exists())
                                <a href="{{ route('requests.mine') }}" wire:navigate class="text-sm font-semibold text-sky-700 hover:text-sky-800">
                                    درخواست‌های من
                                </a>
                            @endif
                            @if (auth()->user()->roles()->where('name', 'owner')->exists())
                                <a href="{{ route('owner.dashboard') }}" wire:navigate class="text-sm font-semibold text-sky-700 hover:text-sky-800">
                                    پنل مالک
                                </a>
                            @endif
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="text-sm font-semibold text-sky-700 hover:text-sky-800">خروج</button>
                            </form>
                        </div>
                    @endguest
                </div>
            </nav>
        </header>

        <main>
            {{ $slot }}
        </main>

        @livewireScripts
    </body>
</html>
