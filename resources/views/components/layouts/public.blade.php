<!doctype html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'en' ? 'ltr' : 'rtl' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#173f5f">
    <meta name="description" content="{{ __('site.description', ['brand' => $siteSettings->brandName()]) }}">
    <link rel="manifest" href="/manifest.webmanifest">
    <link rel="icon" type="image/svg+xml" href="/icons/app-icon.svg">
    <link rel="apple-touch-icon" href="/icons/app-icon.svg">
    <title>{{ $title ?? $siteSettings->brandName() }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
<a class="skip-link" href="#content">{{ __('site.skip_to_content') }}</a>
<header class="site-header">
    <div class="shell nav">
        <a class="brand" href="{{ route('home') }}">{{ $siteSettings->brandName() }}<small>{{ __('site.brand_tagline') }}</small></a>
        <nav aria-label="{{ __('site.navigation') }}">
            <a href="{{ route('catalog') }}">{{ __('site.nav.catalog') }}</a>
            <a href="{{ route('articles.index') }}">{{ __('site.nav.articles') }}</a>
            <a href="{{ route('case-studies.index') }}">{{ __('site.nav.case_studies') }}</a>
            <a href="{{ route('cart.index') }}">{{ __('site.nav.cart') }}</a>
        </nav>
        <div class="locale-links" aria-label="{{ __('site.language') }}">
            @foreach(['fa' => 'فا', 'en' => 'EN', 'ar' => 'ع'] as $code => $label)
                <a class="{{ app()->getLocale() === $code ? 'active' : '' }}" href="{{ request()->fullUrlWithQuery(['locale' => $code]) }}">{{ $label }}</a>
            @endforeach
        </div>
    </div>
    <nav class="shell category-nav" aria-label="{{ __('site.nav.catalog') }}">
        @foreach($navigationCategories as $category)
            @include('components.navigation-category', ['category' => $category])
        @endforeach
    </nav>
</header>
<main id="content" class="shell page">@include('partials.flash'){{ $slot }}</main>
<footer class="site-footer"><div class="shell"><strong>{{ $siteSettings->brandName() }}</strong><p>{{ __('site.footer') }}</p><small>© {{ now()->year }} · {{ __('site.mvp') }}</small></div></footer>
<script>if ('serviceWorker' in navigator) { window.addEventListener('load', () => navigator.serviceWorker.register('/sw.js')); }</script>
</body>
</html>
