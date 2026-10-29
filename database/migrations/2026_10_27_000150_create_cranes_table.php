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
        Schema::create('cranes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained();
            $table->foreignId('crane_type_id')->constrained();
            $table->string('owner_reference');
            $table->string('manufacturer')->nullable();
            $table->string('model')->nullable();
            $table->unsignedSmallInteger('manufacture_year')->nullable();
            $table->unsignedBigInteger('capacity_kg');
            $table->unsignedInteger('boom_length_mm')->nullable();
            $table->text('description')->nullable();
            $table->string('status', 20)->default('draft')->index();
            $table->boolean('is_featured')->default(false)->index();
            $table->timestamps();

            $table->unique(['organization_id', 'owner_reference']);
            $table->index(['crane_type_id', 'status', 'capacity_kg']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cranes');
    }
};
