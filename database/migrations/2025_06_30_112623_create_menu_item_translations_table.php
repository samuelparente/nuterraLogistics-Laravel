<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('menu_item_translations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('menu_item_id')
                ->constrained('menu_items')
                ->onDelete('cascade');

            $table->string('locale', 5);
            $table->foreignId('country_id')
                ->nullable()
                ->constrained('iso_countries')
                ->nullOnDelete();

            $table->string('name')->nullable();
            $table->string('slug')->nullable();
            $table->string('subtitle')->nullable();
            $table->string('short_description')->nullable();
            $table->text('description')->nullable();
            $table->string('meta_title')->nullable();
            $table->string('meta_description')->nullable();
            $table->string('meta_keywords')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->unique(
                ['menu_item_id', 'locale', 'country_id'],
                'menu_item_translations_item_locale_country_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menu_item_translations');
    }
};
