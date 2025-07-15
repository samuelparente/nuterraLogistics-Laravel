<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('unit_of_measure_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('unit_of_measure_id')
                  ->constrained('units_of_measure')
                  ->cascadeOnDelete();
            $table->string('locale', 5);
            $table->unsignedBigInteger('country_id')->nullable();
            $table->string('name');
            $table->timestamps();

            $table->unique(['unit_of_measure_id', 'locale', 'country_id'], 'uomt_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('unit_of_measure_translations');
    }
};
