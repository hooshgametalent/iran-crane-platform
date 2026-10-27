<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\CaseStudy;
use App\Models\Category;
use App\Models\Product;
use App\Models\QuoteRequest;
use App\Services\SiteSettings;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ManagementController extends Controller
{
    private function localized(Request $request, string $name, bool $required = true): array
    {
        $rule = $required ? ['required', 'string', 'max:65535'] : ['nullable', 'string', 'max:65535'];
        $data = $request->validate(["{$name}_fa" => $rule, "{$name}_en" => $rule, "{$name}_ar" => $rule]);

        return ['fa' => $data["{$name}_fa"] ?? '', 'en' => $data["{$name}_en"] ?? '', 'ar' => $data["{$name}_ar"] ?? ''];
    }

    private function catalogAccess(Request $request): void
    {
        abort_unless($request->user()->canManageStore(), 403);
    }

    private function contentAccess(Request $request): void
    {
        abort_unless($request->user()->canManageContent(), 403);
    }

    public function categories(Request $request): View
    {
        $this->catalogAccess($request);

        return view('admin.categories.index', ['categories' => Category::with('parent')->orderBy('sort_order')->orderBy('id')->get()]);
    }

    public function storeCategory(Request $request): RedirectResponse
    {
        $this->catalogAccess($request);
        $data = $request->validate([
            'parent_id' => ['nullable', 'exists:categories,id'],
            'slug' => ['nullable', 'alpha_dash', 'max:120', 'unique:categories,slug'],
            'is_active' => ['boolean'], 'is_demo' => ['boolean'], 'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        DB::transaction(function () use ($request, $data): void {
            $this->validateCategoryParent($data['parent_id'] ?? null);
            Category::create([
                ...$data,
                'title' => $this->localized($request, 'title'),
                'slug' => $data['slug'] ?: Str::slug($request->input('title_en') ?: $request->input('title_fa')).'-'.Str::lower(Str::random(5)),
                'is_active' => $request->boolean('is_active'),
                'is_demo' => $request->boolean('is_demo'),
                'sort_order' => $data['sort_order'] ?? 0,
            ]);
        });

        return back()->with('success', __('site.messages.category_created'));
    }

    public function updateCategory(Request $request, Category $category): RedirectResponse
    {
        $this->catalogAccess($request);
        $data = $request->validate([
            'parent_id' => ['nullable', 'exists:categories,id'],
            'slug' => ['required', 'alpha_dash', 'max:120', 'unique:categories,slug,'.$category->id],
            'is_active' => ['boolean'], 'is_demo' => ['boolean'], 'sort_order' => ['required', 'integer', 'min:0'],
        ]);

        DB::transaction(function () use ($request, $data, $category): void {
            $category = Category::query()->lockForUpdate()->findOrFail($category->id);
            $this->validateCategoryParent($data['parent_id'] ?? null, $category->id);
            $category->update([
                ...$data,
                'title' => $this->localized($request, 'title'),
                'is_active' => $request->boolean('is_active'),
                'is_demo' => $request->boolean('is_demo'),
            ]);
        });

        return back()->with('success', __('site.messages.category_updated'));
    }

    private function validateCategoryParent(?int $parentId, ?int $categoryId = null): void
    {
        $seen = [];
        $currentId = $parentId;

        while ($currentId !== null) {
            if (isset($seen[$currentId]) || ($categoryId !== null && $currentId === $categoryId)) {
                throw ValidationException::withMessages(['parent_id' => __('site.validation.category_cycle')]);
            }

            $seen[$currentId] = true;
            $current = Category::query()->lockForUpdate()->find($currentId);
            if ($current === null) {
                throw ValidationException::withMessages(['parent_id' => __('site.validation.category_parent_missing')]);
            }

            $currentId = $current->parent_id;
        }
    }

    public function products(Request $request): View
    {
        $this->catalogAccess($request);

        return view('admin.products.index', ['products' => Product::with('category')->latest()->paginate(20), 'categories' => Category::orderBy('sort_order')->orderBy('id')->get()]);
    }

    public function createProduct(Request $request): View
    {
        $this->catalogAccess($request);

        return view('admin.products.form', ['product' => new Product(), 'categories' => Category::orderBy('sort_order')->orderBy('id')->get()]);
    }

    public function storeProduct(Request $request): RedirectResponse
    {
        $this->catalogAccess($request);
        $product = Product::create($this->productData($request));

        return redirect()->route('admin.products.index')->with('success', __('site.messages.product_created', ['product' => $product->localized('title')]));
    }

    public function editProduct(Request $request, Product $product): View
    {
        $this->catalogAccess($request);

        return view('admin.products.form', ['product' => $product, 'categories' => Category::orderBy('sort_order')->orderBy('id')->get()]);
    }

    public function updateProduct(Request $request, Product $product): RedirectResponse
    {
        $this->catalogAccess($request);
        $product->update($this->productData($request, $product));

        return redirect()->route('admin.products.index')->with('success', __('site.messages.product_updated'));
    }

    private function productData(Request $request, ?Product $product = null): array
    {
        $id = $product?->id ?? 'NULL';
        $data = $request->validate([
            'category_id' => ['nullable', 'exists:categories,id'],
            'slug' => ['required', 'alpha_dash', 'max:120', 'unique:products,slug,'.$id],
            'price_irr' => ['nullable', 'integer', 'min:0'],
            'sales_mode' => ['required', 'in:inherit,direct,quote,both'],
            'sort_order' => ['required', 'integer', 'min:0'],
            'is_active' => ['boolean'], 'is_featured' => ['boolean'], 'is_demo' => ['boolean'],
        ]);
        $data['price_irr'] = isset($data['price_irr']) ? (int) $data['price_irr'] : null;
        $effectiveMode = $data['sales_mode'] === 'inherit' ? app(SiteSettings::class)->salesMode() : $data['sales_mode'];

        if ($request->boolean('is_active') && in_array($effectiveMode, ['direct', 'both'], true) && ($data['price_irr'] === null || $data['price_irr'] <= 0)) {
            throw ValidationException::withMessages(['price_irr' => __('site.validation.direct_price_required')]);
        }

        return [
            ...$data,
            'title' => $this->localized($request, 'title'),
            'short_description' => $this->localized($request, 'short_description', false),
            'description' => $this->localized($request, 'description', false),
            'is_active' => $request->boolean('is_active'),
            'is_featured' => $request->boolean('is_featured'),
            'is_demo' => $request->boolean('is_demo'),
        ];
    }

    public function articles(Request $request): View { $this->contentAccess($request); return view('admin.articles.index', ['articles' => Article::latest()->paginate(20)]); }
    public function createArticle(Request $request): View { $this->contentAccess($request); return view('admin.articles.form', ['article' => new Article(), 'products' => Product::active()->get()]); }
    public function editArticle(Request $request, Article $article): View { $this->contentAccess($request); return view('admin.articles.form', ['article' => $article->load('products'), 'products' => Product::active()->get()]); }
    public function storeArticle(Request $request): RedirectResponse { $this->contentAccess($request); $article = Article::create($this->articleData($request)); $article->products()->sync($request->input('product_ids', [])); return redirect()->route('admin.articles.index')->with('success', __('site.messages.article_created')); }
    public function updateArticle(Request $request, Article $article): RedirectResponse { $this->contentAccess($request); $article->update($this->articleData($request, $article)); $article->products()->sync($request->input('product_ids', [])); return redirect()->route('admin.articles.index')->with('success', __('site.messages.article_updated')); }

    private function articleData(Request $request, ?Article $article = null): array
    {
        $data = $request->validate(['slug' => ['required', 'alpha_dash', 'max:120', 'unique:articles,slug,'.($article?->id ?? 'NULL')], 'is_published' => ['boolean'], 'is_demo' => ['boolean'], 'published_at' => ['nullable', 'date'], 'product_ids' => ['array'], 'product_ids.*' => ['integer', 'exists:products,id']]);

        return [...$data, 'title' => $this->localized($request, 'title'), 'excerpt' => $this->localized($request, 'excerpt', false), 'body' => $this->localized($request, 'body'), 'is_published' => $request->boolean('is_published'), 'is_demo' => $request->boolean('is_demo')];
    }

    public function caseStudies(Request $request): View { $this->contentAccess($request); return view('admin.case-studies.index', ['caseStudies' => CaseStudy::latest()->paginate(20)]); }
    public function createCaseStudy(Request $request): View { $this->contentAccess($request); return view('admin.case-studies.form', ['caseStudy' => new CaseStudy(), 'products' => Product::active()->get()]); }
    public function editCaseStudy(Request $request, CaseStudy $caseStudy): View { $this->contentAccess($request); return view('admin.case-studies.form', ['caseStudy' => $caseStudy->load('products'), 'products' => Product::active()->get()]); }
    public function storeCaseStudy(Request $request): RedirectResponse { $this->contentAccess($request); $caseStudy = CaseStudy::create($this->caseStudyData($request)); $caseStudy->products()->sync($request->input('product_ids', [])); return redirect()->route('admin.case-studies.index')->with('success', __('site.messages.case_study_created')); }
    public function updateCaseStudy(Request $request, CaseStudy $caseStudy): RedirectResponse { $this->contentAccess($request); $caseStudy->update($this->caseStudyData($request, $caseStudy)); $caseStudy->products()->sync($request->input('product_ids', [])); return redirect()->route('admin.case-studies.index')->with('success', __('site.messages.case_study_updated')); }

    private function caseStudyData(Request $request, ?CaseStudy $caseStudy = null): array
    {
        $data = $request->validate(['slug' => ['required', 'alpha_dash', 'max:120', 'unique:case_studies,slug,'.($caseStudy?->id ?? 'NULL')], 'is_published' => ['boolean'], 'is_demo' => ['boolean'], 'published_at' => ['nullable', 'date'], 'product_ids' => ['array'], 'product_ids.*' => ['integer', 'exists:products,id']]);

        return [...$data, 'title' => $this->localized($request, 'title'), 'customer_name' => $this->localized($request, 'customer_name'), 'summary' => $this->localized($request, 'summary', false), 'body' => $this->localized($request, 'body'), 'is_published' => $request->boolean('is_published'), 'is_demo' => $request->boolean('is_demo')];
    }

    public function quoteRequests(Request $request): View { abort_unless($request->user()->canManageSales(), 403); return view('admin.quotes.index', ['quotes' => QuoteRequest::latest()->paginate(20)]); }
    public function updateQuote(Request $request, QuoteRequest $quote): RedirectResponse { abort_unless($request->user()->canManageSales(), 403); $quote->update($request->validate(['status' => ['required', 'in:new,contacted,closed']])); return back()->with('success', __('site.messages.quote_updated')); }
}
