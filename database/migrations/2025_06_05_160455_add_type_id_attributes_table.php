<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Nova coluna nullable
        Schema::table('attributes', function (Blueprint $table) {
            $table->unsignedBigInteger('attribute_type_id')->nullable()->after('id');
        });

        // 2. Atualizar todos para tipo "select" (ajusta o ID se for diferente!)
        DB::table('attributes')->where('type', 'select')->update(['attribute_type_id' => 3]);

        // 3. Tornar obrigatória e FK
        Schema::table('attributes', function (Blueprint $table) {
            $table->unsignedBigInteger('attribute_type_id')->nullable(false)->change();
            $table->foreign('attribute_type_id')->references('id')->on('attribute_types');
        });

        // 4. Opcional: remover a antiga
        Schema::table('attributes', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }

    public function down(): void
    {
        Schema::table('attributes', function (Blueprint $table) {
            $table->dropForeign(['attribute_type_id']);
            $table->dropColumn('attribute_type_id');
            // $table->string('type')->nullable()->after('id');
        });
    }
};
