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
        Schema::create('monthly_weather', function (Blueprint $table) {
            $table->id();
            $table->foreignId('destination_id')->constrained('destinations')->cascadeOnDelete();
            $table->unsignedTinyInteger('month');
            $table->decimal('avg_temp', 4, 1);
            $table->decimal('min_temp', 4, 1);
            $table->decimal('max_temp', 4, 1);
            $table->unsignedTinyInteger('rainy_days');
            $table->timestamps();

            $table->unique(['destination_id', 'month']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monthly_weather');
    }
};
