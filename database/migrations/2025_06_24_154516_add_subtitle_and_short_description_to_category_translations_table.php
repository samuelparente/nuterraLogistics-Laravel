<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('category_translations', function (Blueprint $table) {
            $table->string('subtitle')->nullable()->after('name');
            $table->string('short_description')->nullable()->after('subtitle');
        });
    }

    public function down(): void
    {
        Schema::table('category_translations', function (Blueprint $table) {
            $table->dropColumn('subtitle');
            $table->dropColumn('short_description');
        });
    }
};
