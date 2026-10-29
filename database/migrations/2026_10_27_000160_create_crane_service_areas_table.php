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
        Schema::create('crane_service_areas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('crane_id')->constrained()->cascadeOnDelete();
            $table->foreignId('province_id')->constrained();
            $table->foreignId('city_id')->nullable()->constrained()->nullOnDelete();
            $table->string('area_key');
            $table->timestamps();

            $table->unique(['crane_id', 'area_key']);
            $table->index(['province_id', 'city_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('crane_service_areas');
    }
};
