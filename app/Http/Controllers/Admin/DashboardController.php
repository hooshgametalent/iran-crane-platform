<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\CaseStudy;
use App\Models\Order;
use App\Models\Product;
use App\Models\QuoteRequest;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $user = $request->user();
        $counts = [];
        $recentOrders = collect();

        if ($user->canManageStore()) {
            $counts['products'] = Product::count();
        }
        if ($user->canManageContent()) {
            $counts['articles'] = Article::count();
            $counts['case_studies'] = CaseStudy::count();
        }
        if ($user->canManageSales()) {
            $counts['quotes'] = QuoteRequest::where('status', 'new')->count();
            $counts['payments'] = Order::where('status', 'payment_submitted')->count();
            $recentOrders = Order::query()->latest()->take(6)->get();
        }

        return view('admin.dashboard', compact('counts', 'recentOrders'));
    }
}
