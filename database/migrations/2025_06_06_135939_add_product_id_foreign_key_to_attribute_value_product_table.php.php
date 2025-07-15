<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProductIdForeignKeyToAttributeValueProductTable extends Migration
{
    public function up()
    {
        Schema::table('product_attribute_value', function (Blueprint $table) {
            // Adiciona a foreign key para product_id se não existir
            $table->foreign('product_id', 'attribute_value_product_product_id_foreign')
                  ->references('id')
                  ->on('products')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('product_attribute_value', function (Blueprint $table) {
            $table->dropForeign('attribute_value_product_product_id_foreign');
        });
    }
}
