<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_translations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->string('locale', 5);
            $table->unsignedBigInteger('country_id')->nullable();
            $table->string('slug')->nullable()->after('country_id');
            $table->string('name');
            $table->string('subtitle')->nullable();
            $table->string('short_description')->nullable();
            $table->text('description')->nullable();
            $table->string('meta_title')->nullable();
            $table->string('meta_description')->nullable();
            $table->string('meta_keywords')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['product_id', 'locale', 'country_id']);
            $table->foreign('product_id')->references('id')->on('products')->cascadeOnDelete();
            $table->foreign('country_id')->references('id')->on('iso_countries')->nullOnDelete();
        });

    }

    public function down(): void
    {
        Schema::dropIfExists('product_translations');
    }
};
