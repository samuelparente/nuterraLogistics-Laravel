<?php
// database/migrations/xxxx_xx_xx_create_brand_translations_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('brand_translations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('brand_id');
            $table->string('locale', 5);
            $table->string('name');
            $table->string('subtitle')->nullable();
            $table->string('short_description')->nullable();
            $table->text('description')->nullable();
            $table->string('meta_title')->nullable();
            $table->string('meta_description')->nullable();
            $table->string('meta_keywords')->nullable();
            $table->string('logo_alt')->nullable();    // SEO imagem
            $table->string('logo_title')->nullable();  // SEO imagem
            $table->unsignedBigInteger('country_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['brand_id', 'locale']);
            $table->foreign('brand_id')->references('id')->on('brands')->onDelete('cascade');            
            $table->foreign('country_id')->references('id')->on('iso_countries');

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('brand_translations');
    }
};
