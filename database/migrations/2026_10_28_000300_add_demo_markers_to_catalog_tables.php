<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table): void {
            $table->boolean('is_demo')->default(false)->index()->after('is_active');
        });

        Schema::table('products', function (Blueprint $table): void {
            $table->boolean('is_demo')->default(false)->index()->after('is_featured');
        });

        // These slugs are the seeded demonstration records from DatabaseSeeder.
        DB::table('categories')->whereIn('slug', ['mobile-cranes', 'industrial-cranes', 'telescopic'])->update(['is_demo' => true]);
        DB::table('products')->whereIn('slug', ['souri-st-30', 'souri-sc-60', 'souri-ib-10'])->update(['is_demo' => true]);
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table): void {
            $table->dropIndex(['is_demo']);
            $table->dropColumn('is_demo');
        });

        Schema::table('categories', function (Blueprint $table): void {
            $table->dropIndex(['is_demo']);
            $table->dropColumn('is_demo');
        });
    }
};
