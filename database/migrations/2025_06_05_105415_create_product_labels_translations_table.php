<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('label_translations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('label_id');
            $table->string('locale', 5);
            $table->unsignedBigInteger('country_id')->nullable();
            $table->string('name');
            $table->string('slug'); // slug específico para cada idioma/país
            $table->string('description')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['label_id', 'locale', 'country_id'], 'lbl_tran_lblid_loc_cty_unique');
            $table->foreign('label_id')->references('id')->on('labels')->cascadeOnDelete();
            $table->foreign('country_id')->references('id')->on('iso_countries')->nullOnDelete();
        });

        // ------ Inserção de dados de exemplo (labels e traduções) ------

        // Cria alguns selos na tabela principal (labels)
        $labels = [
            [
                'color' => '#28a745',
                'is_visible' => true,
            ],
            [
                'color' => '#009688',
                'is_visible' => true,
            ],
            [
                'color' => '#e83e8c',
                'is_visible' => true,
            ],
        ];
        DB::table('labels')->insert($labels);

        // Busca IDs para garantir correspondência correta
        $labelIds = DB::table('labels')->orderBy('id')->pluck('id')->toArray();

        // Traduções para cada selo
        $translations = [
            // --- "Novo" ---
            [
                'label_id'    => $labelIds[0],
                'locale'      => 'pt',
                'country_id'  => 620,
                'name'        => 'Novo',
                'slug'        => 'novo',
                'description' => 'Produto recentemente adicionado',
            ],
            [
                'label_id'    => $labelIds[0],
                'locale'      => 'es',
                'country_id'  => 724,
                'name'        => 'Nuevo',
                'slug'        => 'nuevo',
                'description' => 'Producto añadido recientemente',
            ],
            [
                'label_id'    => $labelIds[0],
                'locale'      => 'en',
                'country_id'  => 840,
                'name'        => 'New',
                'slug'        => 'new',
                'description' => 'Recently added product',
            ],
            // --- "Sustentável" ---
            [
                'label_id'    => $labelIds[1],
                'locale'      => 'pt',
                'country_id'  => 620,
                'name'        => 'Sustentável',
                'slug'        => 'sustentavel',
                'description' => 'Produto amigo do ambiente',
            ],
            [
                'label_id'    => $labelIds[1],
                'locale'      => 'es',
                'country_id'  => 724,
                'name'        => 'Sostenible',
                'slug'        => 'sostenible',
                'description' => 'Producto ecológico',
            ],
            [
                'label_id'    => $labelIds[1],
                'locale'      => 'en',
                'country_id'  => 840,
                'name'        => 'Sustainable',
                'slug'        => 'sustainable',
                'description' => 'Eco-friendly product',
            ],
            // --- "Promoção" ---
            [
                'label_id'    => $labelIds[2],
                'locale'      => 'pt',
                'country_id'  => 620,
                'name'        => 'Promoção',
                'slug'        => 'promocao',
                'description' => 'Produto em promoção especial',
            ],
            [
                'label_id'    => $labelIds[2],
                'locale'      => 'es',
                'country_id'  => 724,
                'name'        => 'Promoción',
                'slug'        => 'promocion',
                'description' => 'Producto en promoción especial',
            ],
            [
                'label_id'    => $labelIds[2],
                'locale'      => 'en',
                'country_id'  => 840,
                'name'        => 'Promotion',
                'slug'        => 'promotion',
                'description' => 'Special promotion product',
            ],
        ];

        DB::table('label_translations')->insert($translations);
    }

    public function down(): void
    {
        Schema::dropIfExists('label_translations');
    }
};
