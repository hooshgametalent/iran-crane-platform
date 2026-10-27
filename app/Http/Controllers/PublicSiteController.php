<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\CaseStudy;
use App\Models\Category;
use App\Models\Product;
use Illuminate\View\View;

class PublicSiteController extends Controller
{
    public function home(): View
    {
        return view('public.home', [
            'featuredProducts' => Product::query()->publiclyVisible()->with(['category' => fn ($query) => $query->publiclyVisible()])->where('is_featured', true)->orderBy('sort_order')->take(6)->get(),
            'articles' => Article::query()->publiclyVisible()->latest('published_at')->take(3)->get(),
            'caseStudies' => CaseStudy::query()->publiclyVisible()->latest('published_at')->take(3)->get(),
        ]);
    }

    public function catalog(?Category $category = null): View
    {
        if ($category !== null) {
            $category = Category::query()->publiclyVisible()->findOrFail($category->id);
        }

        $products = Product::query()->publiclyVisible()->with(['category' => fn ($query) => $query->publiclyVisible()])
            ->when($category, fn ($query) => $query->where('category_id', $category->id))
            ->orderBy('sort_order')->paginate(12);

        return view('public.catalog', compact('products', 'category'));
    }

    public function product(string $slug): View
    {
        $product = Product::query()->publiclyVisible()->with([
            'category' => fn ($query) => $query->publiclyVisible(),
            'articles' => fn ($query) => $query->publiclyVisible(),
            'caseStudies' => fn ($query) => $query->publiclyVisible(),
        ])->where('slug', $slug)->firstOrFail();

        return view('public.product', compact('product'));
    }

    public function articles(): View
    {
        return view('public.articles', ['articles' => Article::query()->publiclyVisible()->latest('published_at')->paginate(10)]);
    }

    public function article(string $slug): View
    {
        return view('public.article', ['article' => Article::query()->publiclyVisible()->with(['products' => fn ($query) => $query->publiclyVisible()])->where('slug', $slug)->firstOrFail()]);
    }

    public function caseStudies(): View
    {
        return view('public.case-studies', ['caseStudies' => CaseStudy::query()->publiclyVisible()->latest('published_at')->paginate(10)]);
    }

    public function caseStudy(string $slug): View
    {
        return view('public.case-study', ['caseStudy' => CaseStudy::query()->publiclyVisible()->with(['products' => fn ($query) => $query->publiclyVisible()])->where('slug', $slug)->firstOrFail()]);
    }
}
