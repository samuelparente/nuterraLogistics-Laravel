<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('attributes', function (Blueprint $table) {
            // Primeiro remove a foreign key
            $table->dropForeign(['attribute_type_id']);
            // Depois remove a coluna
            $table->dropColumn('attribute_type_id');
        });
    }

    public function down()
    {
        Schema::table('attributes', function (Blueprint $table) {
            $table->unsignedBigInteger('attribute_type_id');
            // Se quiseres restaurar a foreign key, assume o nome original da tabela de tipos:
            $table->foreign('attribute_type_id')->references('id')->on('attribute_types');
        });
    }
};
