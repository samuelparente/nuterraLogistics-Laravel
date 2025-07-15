<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('classification_type_translations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('classification_type_id');
            $table->string('locale', 5);
            $table->unsignedBigInteger('country_id')->nullable();
            $table->string('name');
            $table->timestamps();

            $table->unique(['classification_type_id', 'locale', 'country_id'], 'classtype_id_locale_country_unique');
            $table->foreign('classification_type_id')->references('id')->on('classification_types')->cascadeOnDelete();
            $table->foreign('country_id')->references('id')->on('iso_countries')->nullOnDelete();
        });

        // Pré-preencher traduções dos tipos
        $typeIds = DB::table('classification_types')->pluck('id', 'code');

        $translations = [
            // categoria
            ['classification_type_id' => $typeIds['categoria'],    'locale' => 'pt', 'country_id' => 620, 'name' => 'Categoria'],
            ['classification_type_id' => $typeIds['categoria'],    'locale' => 'es', 'country_id' => 724, 'name' => 'Categoría'],
            ['classification_type_id' => $typeIds['categoria'],    'locale' => 'en', 'country_id' => 840, 'name' => 'Category'],
            // subcategoria
            ['classification_type_id' => $typeIds['subcategoria'], 'locale' => 'pt', 'country_id' => 620, 'name' => 'Subcategoria'],
            ['classification_type_id' => $typeIds['subcategoria'], 'locale' => 'es', 'country_id' => 724, 'name' => 'Subcategoría'],
            ['classification_type_id' => $typeIds['subcategoria'], 'locale' => 'en', 'country_id' => 840, 'name' => 'Subcategory'],
            // familia
            ['classification_type_id' => $typeIds['familia'],      'locale' => 'pt', 'country_id' => 620, 'name' => 'Família'],
            ['classification_type_id' => $typeIds['familia'],      'locale' => 'es', 'country_id' => 724, 'name' => 'Familia'],
            ['classification_type_id' => $typeIds['familia'],      'locale' => 'en', 'country_id' => 840, 'name' => 'Family'],
            // subfamilia
            ['classification_type_id' => $typeIds['subfamilia'],   'locale' => 'pt', 'country_id' => 620, 'name' => 'Subfamília'],
            ['classification_type_id' => $typeIds['subfamilia'],   'locale' => 'es', 'country_id' => 724, 'name' => 'Subfamilia'],
            ['classification_type_id' => $typeIds['subfamilia'],   'locale' => 'en', 'country_id' => 840, 'name' => 'Subfamily'],
            // segmento
            ['classification_type_id' => $typeIds['segmento'],     'locale' => 'pt', 'country_id' => 620, 'name' => 'Segmento'],
            ['classification_type_id' => $typeIds['segmento'],     'locale' => 'es', 'country_id' => 724, 'name' => 'Segmento'],
            ['classification_type_id' => $typeIds['segmento'],     'locale' => 'en', 'country_id' => 840, 'name' => 'Segment'],
        ];

        DB::table('classification_type_translations')->insert($translations);
    }

    public function down(): void
    {
        Schema::dropIfExists('classification_type_translations');
    }
};
