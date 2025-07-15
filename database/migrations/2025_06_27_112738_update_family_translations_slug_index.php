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
        Schema::table('family_translations', function (Blueprint $table) {
            // Remove índice antigo
            $table->dropUnique('family_translations_slug_unique');

            // Cria índice novo composto
            $table->unique(['slug', 'locale', 'country_id'], 'family_translations_slug_locale_country_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('family_translations', function (Blueprint $table) {
            $table->dropUnique('family_translations_slug_locale_country_unique');

            $table->unique('slug', 'family_translations_slug_unique');
        });
    }
};
