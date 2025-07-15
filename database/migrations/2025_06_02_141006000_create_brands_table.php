<?php
// database/migrations/xxxx_xx_xx_create_brands_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('brands', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('logo')->nullable();
            $table->string('website')->nullable();
            $table->unsignedBigInteger('status_id')->nullable();
            $table->integer('order')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->string('external_code')->nullable();
            $table->boolean('is_visible')->default(true);
            $table->softDeletes();
            $table->timestamps();
            $table->foreign('status_id')->references('id')->on('statuses');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('brands');
    }
};
