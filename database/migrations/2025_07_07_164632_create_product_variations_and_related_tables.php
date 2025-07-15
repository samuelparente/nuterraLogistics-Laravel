<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        /**
         * product_variations
         *
         * Guarda info para cada variação de produtos do tipo "variable".
         */
        Schema::create('product_variations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')
                ->constrained('products')
                ->onDelete('cascade');
            $table->string('sku')->nullable();
            $table->decimal('price', 10, 2)->nullable();
            $table->decimal('promo_price', 10, 2)->nullable();
            $table->integer('stock')->nullable();

            // Medidas técnicas
            $table->decimal('measure_qty', 10, 3)->nullable();
            $table->foreignId('measure_unit_id')
                ->nullable()
                ->constrained('units_of_measure')
                ->nullOnDelete();

            $table->decimal('weight', 10, 3)->nullable();
            $table->decimal('length', 10, 3)->nullable();
            $table->decimal('width', 10, 3)->nullable();
            $table->decimal('height', 10, 3)->nullable();

            $table->boolean('is_visible')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        /**
         * product_variation_attribute_value
         *
         * Liga variações aos valores dos atributos (e.g. cor vermelho, tamanho M).
         */
        Schema::create('product_variation_attribute_value', function (Blueprint $table) {
            $table->id();
            $table->foreignId('variation_id')
                ->constrained('product_variations')
                ->onDelete('cascade');
            $table->foreignId('attribute_value_id')
                ->constrained('attribute_values')
                ->onDelete('cascade');
            $table->timestamps();
        });

        /**
         * bundle_items
         *
         * Liga produtos do tipo "bundle" aos seus componentes.
         */
        Schema::create('bundle_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bundle_product_id')
                ->constrained('products')
                ->onDelete('cascade');
            $table->foreignId('child_product_id')
                ->constrained('products')
                ->onDelete('cascade');
            $table->integer('quantity')->default(1);
            $table->timestamps();
        });

        /**
         * grouped_product_items
         *
         * Liga produtos do tipo "grouped" aos seus filhos.
         */
        Schema::create('grouped_product_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('grouped_product_id')
                ->constrained('products')
                ->onDelete('cascade');
            $table->foreignId('child_product_id')
                ->constrained('products')
                ->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grouped_product_items');
        Schema::dropIfExists('bundle_items');
        Schema::dropIfExists('product_variation_attribute_value');
        Schema::dropIfExists('product_variations');
    }
};
