<?php

namespace App\Providers;

use App\Models\Category;
use App\Services\SiteSettings;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(SiteSettings::class);
    }

    public function boot(): void
    {
        // Component attributes are evaluated by the consuming public/auth view first.
        View::composer(['components.layouts.public', 'public.*', 'auth.*'], function ($view): void {
            $view->with('siteSettings', app(SiteSettings::class));
            $view->with('navigationCategories', Category::publicNavigation());
        });
    }
}
