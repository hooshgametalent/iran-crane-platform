<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ManagementController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\PaymentSubmissionController;
use App\Http\Controllers\PublicSiteController;
use App\Http\Controllers\QuoteRequestController;
use App\Models\Category;
use Illuminate\Support\Facades\Route;

Route::get('/', [PublicSiteController::class, 'home'])->name('home');
Route::get('/catalog', [PublicSiteController::class, 'catalog'])->name('catalog');
Route::get('/catalog/category/{category:slug}', [PublicSiteController::class, 'catalog'])->name('catalog.category');
Route::get('/products/{slug}', [PublicSiteController::class, 'product'])->name('products.show');
Route::get('/articles', [PublicSiteController::class, 'articles'])->name('articles.index');
Route::get('/articles/{slug}', [PublicSiteController::class, 'article'])->name('articles.show');
Route::get('/case-studies', [PublicSiteController::class, 'caseStudies'])->name('case-studies.index');
Route::get('/case-studies/{slug}', [PublicSiteController::class, 'caseStudy'])->name('case-studies.show');

Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/{product}', [CartController::class, 'store'])->name('cart.store');
Route::patch('/cart/{product}', [CartController::class, 'update'])->name('cart.update');
Route::get('/checkout', [CheckoutController::class, 'create'])->name('checkout.create');
Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
Route::get('/orders/{order}', [CheckoutController::class, 'show'])->name('orders.show');
Route::post('/orders/{order}/payment', [PaymentSubmissionController::class, 'store'])->name('payments.store');
Route::post('/products/{product}/quote-request', [QuoteRequestController::class, 'store'])->name('quotes.store');

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login.store');
});
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->middleware('auth')->name('logout');

Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function (): void {
    Route::get('/', DashboardController::class)->name('dashboard');
    Route::get('/settings', [SettingController::class, 'edit'])->name('settings.edit');
    Route::put('/settings', [SettingController::class, 'update'])->name('settings.update');
    Route::get('/categories', [ManagementController::class, 'categories'])->name('categories.index');
    Route::post('/categories', [ManagementController::class, 'storeCategory'])->name('categories.store');
    Route::put('/categories/{category}', [ManagementController::class, 'updateCategory'])->name('categories.update');
    Route::get('/products', [ManagementController::class, 'products'])->name('products.index');
    Route::get('/products/create', [ManagementController::class, 'createProduct'])->name('products.create');
    Route::post('/products', [ManagementController::class, 'storeProduct'])->name('products.store');
    Route::get('/products/{product}/edit', [ManagementController::class, 'editProduct'])->name('products.edit');
    Route::put('/products/{product}', [ManagementController::class, 'updateProduct'])->name('products.update');
    Route::get('/articles', [ManagementController::class, 'articles'])->name('articles.index');
    Route::get('/articles/create', [ManagementController::class, 'createArticle'])->name('articles.create');
    Route::post('/articles', [ManagementController::class, 'storeArticle'])->name('articles.store');
    Route::get('/articles/{article}/edit', [ManagementController::class, 'editArticle'])->name('articles.edit');
    Route::put('/articles/{article}', [ManagementController::class, 'updateArticle'])->name('articles.update');
    Route::get('/case-studies', [ManagementController::class, 'caseStudies'])->name('case-studies.index');
    Route::get('/case-studies/create', [ManagementController::class, 'createCaseStudy'])->name('case-studies.create');
    Route::post('/case-studies', [ManagementController::class, 'storeCaseStudy'])->name('case-studies.store');
    Route::get('/case-studies/{caseStudy}/edit', [ManagementController::class, 'editCaseStudy'])->name('case-studies.edit');
    Route::put('/case-studies/{caseStudy}', [ManagementController::class, 'updateCaseStudy'])->name('case-studies.update');
    Route::get('/quote-requests', [ManagementController::class, 'quoteRequests'])->name('quotes.index');
    Route::put('/quote-requests/{quote}', [ManagementController::class, 'updateQuote'])->name('quotes.update');
    Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
    Route::put('/orders/{order}/decision', [AdminOrderController::class, 'decide'])->name('orders.decide');
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::put('/users/{user}/role', [UserController::class, 'update'])->name('users.update');
});
