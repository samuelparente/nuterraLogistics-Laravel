<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Produto <-> Classificações (categorias, famílias, segmentos, etc.)
        Schema::create('product_product_classification', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('classification_id');
            $table->timestamps();

            $table->unique(['product_id', 'classification_id'], 'product_classification_unique');
            $table->foreign('product_id')->references('id')->on('products')->cascadeOnDelete();
            $table->foreign('classification_id')->references('id')->on('product_classifications')->cascadeOnDelete();
        });

        // Produto <-> Labels (selos)
        Schema::create('label_product', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('label_id');
            $table->unsignedBigInteger('product_id');
            $table->timestamps();

            $table->unique(['label_id', 'product_id'], 'label_product_unique');
            $table->foreign('label_id')->references('id')->on('labels')->cascadeOnDelete();
            $table->foreign('product_id')->references('id')->on('products')->cascadeOnDelete();
        });

        // Produto <-> Tags
        Schema::create('product_tag', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('tag_id');
            $table->timestamps();

            $table->unique(['product_id', 'tag_id'], 'product_tag_unique');
            $table->foreign('product_id')->references('id')->on('products')->cascadeOnDelete();
            $table->foreign('tag_id')->references('id')->on('tags')->cascadeOnDelete();
        });

        // Produto <-> Valores de atributos
        Schema::create('product_attribute_value', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('attribute_value_id');
            $table->timestamps();

            $table->unique(['product_id', 'attribute_value_id'], 'product_attribute_value_unique');
            $table->foreign('product_id')->references('id')->on('products')->cascadeOnDelete();
            $table->foreign('attribute_value_id')->references('id')->on('attribute_values')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_attribute_value');
        Schema::dropIfExists('product_tag');
        Schema::dropIfExists('label_product');
        Schema::dropIfExists('product_product_classification');
    }
};
