<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Adiciona a coluna como nullable inicialmente
        Schema::table('product_attribute_value', function (Blueprint $table) {
            $table->unsignedBigInteger('attribute_id')->nullable()->after('attribute_value_id');
        });

        // 2. Preenche os dados já existentes
        DB::statement("
            UPDATE product_attribute_value pav
            INNER JOIN attribute_values av
                ON pav.attribute_value_id = av.id
            SET pav.attribute_id = av.attribute_id
        ");

        // 3. Opcional:  tornar NOT NULL, agora já é seguro
        Schema::table('product_attribute_value', function (Blueprint $table) {
            $table->unsignedBigInteger('attribute_id')->nullable(false)->change();
        });
    }

    public function down(): void
    {
        Schema::table('product_attribute_value', function (Blueprint $table) {
            $table->dropColumn('attribute_id');
        });
    }
};
