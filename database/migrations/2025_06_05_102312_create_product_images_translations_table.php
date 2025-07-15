<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_image_translations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_image_id');
            $table->string('locale', 5);
            $table->unsignedBigInteger('country_id')->nullable();
            $table->string('alt')->nullable();    // SEO alt text
            $table->string('title')->nullable();  // SEO title
            $table->timestamps();
            $table->softDeletes();

            // Só precisas deste unique, com nome curto:
            $table->unique(['product_image_id', 'locale', 'country_id'], 'img_transl_pid_loc_cty_unique');
            $table->foreign('product_image_id')->references('id')->on('product_images')->cascadeOnDelete();
            $table->foreign('country_id')->references('id')->on('iso_countries')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_image_translations');
    }
};
