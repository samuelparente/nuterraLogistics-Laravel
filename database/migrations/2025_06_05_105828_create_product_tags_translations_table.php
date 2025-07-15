<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tag_translations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tag_id');
            $table->string('locale', 5);
            $table->unsignedBigInteger('country_id')->nullable();
            $table->string('name');
            $table->string('slug');
            $table->string('description')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['tag_id', 'locale', 'country_id'], 'tag_tran_tagid_loc_cty_unique');
            $table->foreign('tag_id')->references('id')->on('tags')->cascadeOnDelete();
            $table->foreign('country_id')->references('id')->on('iso_countries')->nullOnDelete();
        });

        // ------ Inserção de dados de exemplo (tags e traduções) ------

        $tags = [
            ['is_visible' => true],
            ['is_visible' => true],
            ['is_visible' => true],
        ];
        DB::table('tags')->insert($tags);

        $tagIds = DB::table('tags')->orderBy('id')->pluck('id')->toArray();

        $translations = [
            // --- "Orgânico" ---
            [
                'tag_id'      => $tagIds[0],
                'locale'      => 'pt',
                'country_id'  => 620,
                'name'        => 'Orgânico',
                'slug'        => 'organico',
                'description' => 'Produto com certificação orgânica',
            ],
            [
                'tag_id'      => $tagIds[0],
                'locale'      => 'es',
                'country_id'  => 724,
                'name'        => 'Orgánico',
                'slug'        => 'organico',
                'description' => 'Producto certificado orgánico',
            ],
            [
                'tag_id'      => $tagIds[0],
                'locale'      => 'en',
                'country_id'  => 840,
                'name'        => 'Organic',
                'slug'        => 'organic',
                'description' => 'Certified organic product',
            ],
            // --- "Vegan" ---
            [
                'tag_id'      => $tagIds[1],
                'locale'      => 'pt',
                'country_id'  => 620,
                'name'        => 'Vegan',
                'slug'        => 'vegan',
                'description' => 'Produto sem ingredientes de origem animal',
            ],
            [
                'tag_id'      => $tagIds[1],
                'locale'      => 'es',
                'country_id'  => 724,
                'name'        => 'Vegano',
                'slug'        => 'vegano',
                'description' => 'Producto sin ingredientes de origen animal',
            ],
            [
                'tag_id'      => $tagIds[1],
                'locale'      => 'en',
                'country_id'  => 840,
                'name'        => 'Vegan',
                'slug'        => 'vegan',
                'description' => 'Product with no animal ingredients',
            ],
            // --- "Sem Glúten" ---
            [
                'tag_id'      => $tagIds[2],
                'locale'      => 'pt',
                'country_id'  => 620,
                'name'        => 'Sem Glúten',
                'slug'        => 'sem-gluten',
                'description' => 'Produto isento de glúten',
            ],
            [
                'tag_id'      => $tagIds[2],
                'locale'      => 'es',
                'country_id'  => 724,
                'name'        => 'Sin Gluten',
                'slug'        => 'sin-gluten',
                'description' => 'Producto libre de gluten',
            ],
            [
                'tag_id'      => $tagIds[2],
                'locale'      => 'en',
                'country_id'  => 840,
                'name'        => 'Gluten Free',
                'slug'        => 'gluten-free',
                'description' => 'Gluten-free product',
            ],
        ];

        DB::table('tag_translations')->insert($translations);
    }

    public function down(): void
    {
        Schema::dropIfExists('tag_translations');
    }
};
