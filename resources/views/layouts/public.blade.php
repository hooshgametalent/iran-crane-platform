<!doctype html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'en' ? 'ltr' : 'rtl' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#173f5f">
    <meta name="description" content="{{ $siteSettings->brandName() }}، تولیدکننده و راهکارهای جرثقیل">
    <link rel="manifest" href="/manifest.webmanifest">
    <title>{{ $title ?? $siteSettings->brandName() }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
<a class="skip-link" href="#content">پرش به محتوا</a>
<header class="site-header"><div class="shell nav">
    <a class="brand" href="{{ route('home') }}">{{ $siteSettings->brandName() }}<small>Crane systems</small></a>
    <nav aria-label="ناوبری اصلی"><a href="{{ route('catalog') }}">محصولات</a><a href="{{ route('articles.index') }}">دانش فنی</a><a href="{{ route('case-studies.index') }}">تجربه مشتریان</a><a href="{{ route('cart.index') }}">سبد خرید</a></nav>
    <div class="locale-links" aria-label="زبان">@foreach(['fa' => 'فا', 'en' => 'EN', 'ar' => 'ع'] as $code => $label)<a class="{{ app()->getLocale() === $code ? 'active' : '' }}" href="{{ request()->fullUrlWithQuery(['locale' => $code]) }}">{{ $label }}</a>@endforeach</div>
</div><div class="shell category-nav">@foreach($navigationCategories as $category)<span><a href="{{ route('catalog.category', $category) }}">{{ $category->localized('title') }}</a>@if($category->children->isNotEmpty())<span class="submenu">@foreach($category->children as $child)<a href="{{ route('catalog.category', $child) }}">{{ $child->localized('title') }}</a>@endforeach</span>@endif</span>@endforeach</div></header>
<main id="content" class="shell page">@include('partials.flash'){{ $slot }}</main>
<footer class="site-footer"><div class="shell"><strong>{{ $siteSettings->brandName() }}</strong><p>محتوای فنی، فروش مستقیم و استعلام پروژه، در یک مسیر شفاف.</p><small>© {{ now()->year }} · نسخه MVP</small></div></footer>
<script>if ('serviceWorker' in navigator) { window.addEventListener('load', () => navigator.serviceWorker.register('/sw.js')); }</script>
</body></html>
