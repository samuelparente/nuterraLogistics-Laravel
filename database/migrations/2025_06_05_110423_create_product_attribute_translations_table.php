<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attribute_translations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('attribute_id');
            $table->string('locale', 5);
            $table->unsignedBigInteger('country_id')->nullable();
            $table->string('name');
            $table->string('slug');
            $table->string('description')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['attribute_id', 'locale', 'country_id'], 'attr_tran_attrid_loc_cty_unique');
            $table->foreign('attribute_id')->references('id')->on('attributes')->cascadeOnDelete();
            $table->foreign('country_id')->references('id')->on('iso_countries')->nullOnDelete();
        });

        // Exemplos: Sabor, Tamanho, Cor
        $attributes = [
            ['type' => 'select', 'is_visible' => true, 'is_filterable' => true,  'position' => 0], // Sabor
            ['type' => 'select', 'is_visible' => true, 'is_filterable' => true,  'position' => 1], // Tamanho
            ['type' => 'select', 'is_visible' => true, 'is_filterable' => true,  'position' => 2], // Cor
        ];
        DB::table('attributes')->insert($attributes);
        $attrIds = DB::table('attributes')->orderBy('id')->pluck('id')->toArray();

        $translations = [
            // SABOR
            ['attribute_id' => $attrIds[0], 'locale' => 'pt', 'country_id' => 620, 'name' => 'Sabor',   'slug' => 'sabor',   'description' => 'Escolha de sabor'],
            ['attribute_id' => $attrIds[0], 'locale' => 'es', 'country_id' => 724, 'name' => 'Sabor',   'slug' => 'sabor',   'description' => 'Elección de sabor'],
            ['attribute_id' => $attrIds[0], 'locale' => 'en', 'country_id' => 840, 'name' => 'Flavour', 'slug' => 'flavour', 'description' => 'Flavour choice'],
            // TAMANHO
            ['attribute_id' => $attrIds[1], 'locale' => 'pt', 'country_id' => 620, 'name' => 'Tamanho',   'slug' => 'tamanho',   'description' => 'Escolha de tamanho'],
            ['attribute_id' => $attrIds[1], 'locale' => 'es', 'country_id' => 724, 'name' => 'Tamaño',    'slug' => 'tamano',    'description' => 'Elección de tamaño'],
            ['attribute_id' => $attrIds[1], 'locale' => 'en', 'country_id' => 840, 'name' => 'Size',      'slug' => 'size',      'description' => 'Size choice'],
            // COR
            ['attribute_id' => $attrIds[2], 'locale' => 'pt', 'country_id' => 620, 'name' => 'Cor',    'slug' => 'cor',    'description' => 'Escolha de cor'],
            ['attribute_id' => $attrIds[2], 'locale' => 'es', 'country_id' => 724, 'name' => 'Color',  'slug' => 'color',  'description' => 'Elección de color'],
            ['attribute_id' => $attrIds[2], 'locale' => 'en', 'country_id' => 840, 'name' => 'Colour', 'slug' => 'colour', 'description' => 'Colour choice'],
        ];

        DB::table('attribute_translations')->insert($translations);
    }

    public function down(): void
    {
        Schema::dropIfExists('attribute_translations');
    }
};
