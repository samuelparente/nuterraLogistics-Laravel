<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('receiving_items', function (Blueprint $table) {
            // Coluna booleana, pode ser nula, mas se não for enviada assume 0
            $table->boolean('is_new')
                ->nullable()
                ->default(0)
                ->after('order_item_id'); 
        });
    }

    public function down(): void
    {
        Schema::table('receiving_items', function (Blueprint $table) {
            $table->dropColumn('is_new');
        });
    }
};
