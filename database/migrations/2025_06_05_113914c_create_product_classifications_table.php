<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('product_classifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->unsignedBigInteger('classification_type_id');
            $table->boolean('is_visible')->default(true);
            $table->integer('position')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('parent_id')->references('id')->on('product_classifications')->nullOnDelete();
            $table->foreign('classification_type_id')->references('id')->on('classification_types')->cascadeOnDelete();
        });
    }

    public function down()
    {
        Schema::dropIfExists('product_classifications');
    }
};
