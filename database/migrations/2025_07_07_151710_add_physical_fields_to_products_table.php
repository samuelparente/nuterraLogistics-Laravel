<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('measure_qty', 10, 3)->nullable()->after('stock');
            $table->unsignedBigInteger('measure_unit_id')->nullable()->after('measure_qty');

            $table->decimal('weight', 10, 3)->nullable()->after('measure_unit_id');
            $table->unsignedBigInteger('weight_unit_id')->nullable()->after('weight');

            $table->decimal('length', 10, 3)->nullable()->after('weight_unit_id');
            $table->decimal('width', 10, 3)->nullable()->after('length');
            $table->decimal('height', 10, 3)->nullable()->after('width');
            $table->unsignedBigInteger('dimension_unit_id')->nullable()->after('height');

            $table->boolean('is_special')->default(false)->after('dimension_unit_id');

            $table->foreign('measure_unit_id')
                  ->references('id')
                  ->on('units_of_measure')
                  ->nullOnDelete();

            $table->foreign('weight_unit_id')
                  ->references('id')
                  ->on('units_of_measure')
                  ->nullOnDelete();

            $table->foreign('dimension_unit_id')
                  ->references('id')
                  ->on('units_of_measure')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['measure_unit_id']);
            $table->dropForeign(['weight_unit_id']);
            $table->dropForeign(['dimension_unit_id']);

            $table->dropColumn([
                'measure_qty',
                'measure_unit_id',
                'weight',
                'weight_unit_id',
                'length',
                'width',
                'height',
                'dimension_unit_id',
                'is_special',
            ]);
        });
    }
};
