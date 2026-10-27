<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table): void {
            $table->id();
            $table->string('key')->unique();
            $table->json('value');
            $table->timestamps();
        });

        Schema::create('categories', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('parent_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->json('title');
            $table->string('slug')->unique();
            $table->boolean('is_active')->default(true)->index();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('products', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->json('title');
            $table->json('short_description')->nullable();
            $table->json('description')->nullable();
            $table->string('slug')->unique();
            $table->unsignedBigInteger('price_irr')->nullable();
            $table->string('sales_mode', 16)->default('inherit');
            $table->boolean('is_active')->default(true)->index();
            $table->boolean('is_featured')->default(false)->index();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('articles', function (Blueprint $table): void {
            $table->id();
            $table->json('title');
            $table->json('excerpt')->nullable();
            $table->json('body');
            $table->string('slug')->unique();
            $table->boolean('is_published')->default(false)->index();
            $table->boolean('is_demo')->default(true)->index();
            $table->timestamp('published_at')->nullable()->index();
            $table->timestamps();
        });

        Schema::create('article_product', function (Blueprint $table): void {
            $table->foreignId('article_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->primary(['article_id', 'product_id']);
        });

        Schema::create('case_studies', function (Blueprint $table): void {
            $table->id();
            $table->json('title');
            $table->json('customer_name');
            $table->json('summary')->nullable();
            $table->json('body');
            $table->string('slug')->unique();
            $table->boolean('is_published')->default(false)->index();
            $table->boolean('is_demo')->default(true)->index();
            $table->timestamp('published_at')->nullable()->index();
            $table->timestamps();
        });

        Schema::create('case_study_product', function (Blueprint $table): void {
            $table->foreignId('case_study_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->primary(['case_study_id', 'product_id']);
        });

        Schema::create('quote_requests', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('email');
            $table->string('phone', 40)->nullable();
            $table->string('company')->nullable();
            $table->text('message')->nullable();
            $table->json('product_snapshot')->nullable();
            $table->string('status', 24)->default('new')->index();
            $table->timestamps();
        });

        Schema::create('orders', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('number')->unique();
            $table->string('customer_name');
            $table->string('email');
            $table->string('phone', 40);
            $table->text('address')->nullable();
            $table->unsignedBigInteger('subtotal_irr');
            $table->unsignedBigInteger('total_irr');
            $table->string('status', 32)->default('pending_payment')->index();
            $table->timestamps();
        });

        Schema::create('order_items', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->json('product_title');
            $table->unsignedInteger('quantity');
            $table->unsignedBigInteger('unit_price_irr');
            $table->unsignedBigInteger('line_total_irr');
            $table->timestamps();
        });

        Schema::create('payment_submissions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('order_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('reference', 120);
            $table->string('payer_name')->nullable();
            $table->string('method', 32)->default('bank_transfer');
            $table->unsignedBigInteger('amount_irr');
            $table->text('note')->nullable();
            $table->string('status', 24)->default('submitted')->index();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('order_events', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('payment_submission_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('actor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('event', 64);
            $table->string('from_status', 32)->nullable();
            $table->string('to_status', 32)->nullable();
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_events');
        Schema::dropIfExists('payment_submissions');
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('quote_requests');
        Schema::dropIfExists('case_study_product');
        Schema::dropIfExists('case_studies');
        Schema::dropIfExists('article_product');
        Schema::dropIfExists('articles');
        Schema::dropIfExists('products');
        Schema::dropIfExists('categories');
        Schema::dropIfExists('settings');
    }
};
