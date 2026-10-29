<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('rental_bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rental_quote_id')->constrained()->unique();
            $table->foreignId('rental_request_id')->constrained();
            $table->foreignId('crane_id')->constrained();
            $table->foreignId('customer_user_id')->constrained('users');
            $table->foreignId('customer_organization_id')->nullable()->constrained('organizations')->nullOnDelete();
            $table->foreignId('provider_organization_id')->constrained('organizations');
            $table->timestamp('starts_at');
            $table->timestamp('ends_at');
            $table->unsignedBigInteger('quoted_amount');
            $table->unsignedBigInteger('transport_fee')->nullable();
            $table->unsignedBigInteger('total_amount');
            $table->char('currency', 3)->default('IRR');
            $table->string('status', 20)->default('pending')->index();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamps();

            $table->index(['crane_id', 'status', 'starts_at', 'ends_at']);
            $table->index(['customer_user_id', 'status']);
            $table->index(['provider_organization_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rental_bookings');
    }
};
