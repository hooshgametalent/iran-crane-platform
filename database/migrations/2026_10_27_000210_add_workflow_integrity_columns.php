<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('platform_settings', function (Blueprint $table) {
            $table->unsignedInteger('lock_version')->default(1);
        });

        Schema::table('cranes', function (Blueprint $table) {
            $table->unsignedInteger('lock_version')->default(1);
        });

        Schema::table('rental_requests', function (Blueprint $table) {
            $table->uuid('submission_key')->nullable();
            $table->unique(['user_id', 'submission_key'], 'rental_requests_user_submission_unique');
        });

        Schema::table('rental_quotes', function (Blueprint $table) {
            $table->uuid('submission_key')->nullable();
            $table->unique(['submitted_by_user_id', 'submission_key'], 'rental_quotes_user_submission_unique');
            $table->unique(['rental_request_id', 'crane_id'], 'rental_quotes_request_crane_unique');
        });

        $timestamp = now();

        DB::table('roles')->insertOrIgnore([
            ['name' => 'admin', 'label' => 'مدیر سامانه', 'description' => 'مدیریت تنظیمات و داده‌های پلتفرم.', 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['name' => 'owner', 'label' => 'مالک جرثقیل', 'description' => 'مدیریت جرثقیل‌ها و ارسال پیشنهاد قیمت.', 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['name' => 'customer', 'label' => 'کارفرما', 'description' => 'ثبت درخواست اجاره و پذیرش پیشنهاد.', 'created_at' => $timestamp, 'updated_at' => $timestamp],
        ]);

        DB::table('crane_types')->insertOrIgnore([
            ['name' => 'جرثقیل موبایل', 'slug' => 'mobile-crane', 'sort_order' => 1, 'is_active' => true, 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['name' => 'جرثقیل کفی', 'slug' => 'truck-crane', 'sort_order' => 2, 'is_active' => true, 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['name' => 'جرثقیل تلسکوپی', 'slug' => 'telescopic-crane', 'sort_order' => 3, 'is_active' => true, 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['name' => 'تاورکرین', 'slug' => 'tower-crane', 'sort_order' => 4, 'is_active' => true, 'created_at' => $timestamp, 'updated_at' => $timestamp],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rental_quotes', function (Blueprint $table) {
            $table->dropUnique('rental_quotes_user_submission_unique');
            $table->dropUnique('rental_quotes_request_crane_unique');
            $table->dropColumn('submission_key');
        });

        Schema::table('rental_requests', function (Blueprint $table) {
            $table->dropUnique('rental_requests_user_submission_unique');
            $table->dropColumn('submission_key');
        });

        Schema::table('cranes', function (Blueprint $table) {
            $table->dropColumn('lock_version');
        });

        Schema::table('platform_settings', function (Blueprint $table) {
            $table->dropColumn('lock_version');
        });
    }
};
