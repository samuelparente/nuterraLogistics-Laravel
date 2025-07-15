<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attribute_value_translations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('attribute_value_id');
            $table->string('locale', 5);
            $table->unsignedBigInteger('country_id')->nullable();
            $table->string('value');
            $table->string('slug');
            $table->string('description')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['attribute_value_id', 'locale', 'country_id'], 'attrval_tran_valid_loc_cty_unique');
            $table->foreign('attribute_value_id')->references('id')->on('attribute_values')->cascadeOnDelete();
            $table->foreign('country_id')->references('id')->on('iso_countries')->nullOnDelete();
        });

        // --- Preencher valores exemplo ---
        // Sabor: Chocolate, Baunilha | Tamanho: Pequeno, Médio, Grande | Cor: Vermelho, Verde, Azul
        $attrIds = DB::table('attributes')->orderBy('id')->pluck('id')->toArray();
        $values = [
            // Sabor
            ['attribute_id' => $attrIds[0], 'position' => 0], // Chocolate
            ['attribute_id' => $attrIds[0], 'position' => 1], // Baunilha
            // Tamanho
            ['attribute_id' => $attrIds[1], 'position' => 0], // Pequeno
            ['attribute_id' => $attrIds[1], 'position' => 1], // Médio
            ['attribute_id' => $attrIds[1], 'position' => 2], // Grande
            // Cor
            ['attribute_id' => $attrIds[2], 'position' => 0], // Vermelho
            ['attribute_id' => $attrIds[2], 'position' => 1], // Verde
            ['attribute_id' => $attrIds[2], 'position' => 2], // Azul
        ];
        DB::table('attribute_values')->insert($values);
        $valueIds = DB::table('attribute_values')->orderBy('id')->pluck('id')->toArray();

        $translations = [
            // --- Chocolate ---
            ['attribute_value_id' => $valueIds[0], 'locale' => 'pt', 'country_id' => 620, 'value' => 'Chocolate', 'slug' => 'chocolate'],
            ['attribute_value_id' => $valueIds[0], 'locale' => 'es', 'country_id' => 724, 'value' => 'Chocolate', 'slug' => 'chocolate'],
            ['attribute_value_id' => $valueIds[0], 'locale' => 'en', 'country_id' => 840, 'value' => 'Chocolate', 'slug' => 'chocolate'],
            // --- Baunilha ---
            ['attribute_value_id' => $valueIds[1], 'locale' => 'pt', 'country_id' => 620, 'value' => 'Baunilha', 'slug' => 'baunilha'],
            ['attribute_value_id' => $valueIds[1], 'locale' => 'es', 'country_id' => 724, 'value' => 'Vainilla', 'slug' => 'vainilla'],
            ['attribute_value_id' => $valueIds[1], 'locale' => 'en', 'country_id' => 840, 'value' => 'Vanilla', 'slug' => 'vanilla'],
            // --- Pequeno ---
            ['attribute_value_id' => $valueIds[2], 'locale' => 'pt', 'country_id' => 620, 'value' => 'Pequeno', 'slug' => 'pequeno'],
            ['attribute_value_id' => $valueIds[2], 'locale' => 'es', 'country_id' => 724, 'value' => 'Pequeño', 'slug' => 'pequeno'],
            ['attribute_value_id' => $valueIds[2], 'locale' => 'en', 'country_id' => 840, 'value' => 'Small',    'slug' => 'small'],
            // --- Médio ---
            ['attribute_value_id' => $valueIds[3], 'locale' => 'pt', 'country_id' => 620, 'value' => 'Médio', 'slug' => 'medio'],
            ['attribute_value_id' => $valueIds[3], 'locale' => 'es', 'country_id' => 724, 'value' => 'Mediano', 'slug' => 'mediano'],
            ['attribute_value_id' => $valueIds[3], 'locale' => 'en', 'country_id' => 840, 'value' => 'Medium',  'slug' => 'medium'],
            // --- Grande ---
            ['attribute_value_id' => $valueIds[4], 'locale' => 'pt', 'country_id' => 620, 'value' => 'Grande', 'slug' => 'grande'],
            ['attribute_value_id' => $valueIds[4], 'locale' => 'es', 'country_id' => 724, 'value' => 'Grande', 'slug' => 'grande'],
            ['attribute_value_id' => $valueIds[4], 'locale' => 'en', 'country_id' => 840, 'value' => 'Large',   'slug' => 'large'],
            // --- Vermelho ---
            ['attribute_value_id' => $valueIds[5], 'locale' => 'pt', 'country_id' => 620, 'value' => 'Vermelho', 'slug' => 'vermelho'],
            ['attribute_value_id' => $valueIds[5], 'locale' => 'es', 'country_id' => 724, 'value' => 'Rojo',     'slug' => 'rojo'],
            ['attribute_value_id' => $valueIds[5], 'locale' => 'en', 'country_id' => 840, 'value' => 'Red',      'slug' => 'red'],
            // --- Verde ---
            ['attribute_value_id' => $valueIds[6], 'locale' => 'pt', 'country_id' => 620, 'value' => 'Verde', 'slug' => 'verde'],
            ['attribute_value_id' => $valueIds[6], 'locale' => 'es', 'country_id' => 724, 'value' => 'Verde', 'slug' => 'verde'],
            ['attribute_value_id' => $valueIds[6], 'locale' => 'en', 'country_id' => 840, 'value' => 'Green', 'slug' => 'green'],
            // --- Azul ---
            ['attribute_value_id' => $valueIds[7], 'locale' => 'pt', 'country_id' => 620, 'value' => 'Azul', 'slug' => 'azul'],
            ['attribute_value_id' => $valueIds[7], 'locale' => 'es', 'country_id' => 724, 'value' => 'Azul', 'slug' => 'azul'],
            ['attribute_value_id' => $valueIds[7], 'locale' => 'en', 'country_id' => 840, 'value' => 'Blue', 'slug' => 'blue'],
        ];

        DB::table('attribute_value_translations')->insert($translations);
    }

    public function down(): void
    {
        Schema::dropIfExists('attribute_value_translations');
    }
};
