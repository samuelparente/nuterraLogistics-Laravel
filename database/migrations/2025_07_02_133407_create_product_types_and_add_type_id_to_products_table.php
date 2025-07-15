<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Cria a tabela product_types
        Schema::create('product_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('icon')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('order')->nullable();
            $table->string('external_code')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // Pré-popula a tabela product_types com nomes em PT
        DB::table('product_types')->insert([
            [
                'name' => 'Simples',
                'slug' => 'simple',
                'description' => 'Produto individual sem variações.',
                'icon' => 'fa-box',
                'is_active' => true,
                'order' => 1,
                'external_code' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Variável',
                'slug' => 'variable',
                'description' => 'Produto que permite selecionar atributos como tamanho ou cor.',
                'icon' => 'fa-cubes',
                'is_active' => true,
                'order' => 2,
                'external_code' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Agrupado',
                'slug' => 'grouped',
                'description' => 'Grupo de produtos apresentados em conjunto.',
                'icon' => 'fa-layer-group',
                'is_active' => true,
                'order' => 3,
                'external_code' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Externo',
                'slug' => 'external',
                'description' => 'Produto vendido através de um link externo.',
                'icon' => 'fa-external-link-alt',
                'is_active' => true,
                'order' => 4,
                'external_code' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Conjunto',
                'slug' => 'bundle',
                'description' => 'Conjunto de produtos vendidos como pacote.',
                'icon' => 'fa-boxes',
                'is_active' => true,
                'order' => 5,
                'external_code' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Adiciona a coluna type_id à tabela products
        Schema::table('products', function (Blueprint $table) {
            $table->foreignId('type_id')
                ->nullable()
                ->after('barcode')
                ->constrained('product_types')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        // Remove a foreign key e coluna de products
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['type_id']);
            $table->dropColumn('type_id');
        });

        // Apaga a tabela product_types
        Schema::dropIfExists('product_types');
    }
};
