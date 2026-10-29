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
        Schema::create('rental_quotes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rental_request_id')->constrained();
            $table->foreignId('crane_id')->constrained();
            $table->foreignId('organization_id')->constrained();
            $table->foreignId('submitted_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->unsignedBigInteger('amount');
            $table->char('currency', 3)->default('IRR');
            $table->string('billing_unit', 20)->default('day');
            $table->unsignedBigInteger('transport_fee')->nullable();
            $table->text('terms')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->string('status', 20)->default('sent')->index();
            $table->timestamps();

            $table->index(['rental_request_id', 'status']);
            $table->index(['organization_id', 'status']);
            $table->index(['crane_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rental_quotes');
    }
};
