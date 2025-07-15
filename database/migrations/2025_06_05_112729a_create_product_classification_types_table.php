<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('classification_types', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // categoria, subcategoria, familia, subfamilia, segmento
            $table->boolean('is_visible')->default(true);
            $table->timestamps();
        });

        // Pré-preencher tipos
        $types = [
            ['code' => 'categoria',    'is_visible' => true],
            ['code' => 'subcategoria', 'is_visible' => true],
            ['code' => 'familia',      'is_visible' => true],
            ['code' => 'subfamilia',   'is_visible' => true],
            ['code' => 'segmento',     'is_visible' => true],
        ];
        DB::table('classification_types')->insert($types);
    }

    public function down(): void
    {
        Schema::dropIfExists('classification_types');
    }
};
