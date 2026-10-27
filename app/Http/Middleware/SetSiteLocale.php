<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetSiteLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->query('locale', $request->session()->get('locale', 'fa'));
        $locale = in_array($locale, ['fa', 'en', 'ar'], true) ? $locale : 'fa';

        $request->session()->put('locale', $locale);
        app()->setLocale($locale);

        return $next($request);
    }
}
