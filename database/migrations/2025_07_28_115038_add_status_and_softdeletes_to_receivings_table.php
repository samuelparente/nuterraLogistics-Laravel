<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('receivings', function (Blueprint $table) {
            // status_id: referência à tabela statuses
            $table->unsignedBigInteger('status_id')->nullable()->after('supplier_id');
            $table->foreign('status_id')->references('id')->on('statuses');

            // Soft deletes
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('receivings', function (Blueprint $table) {
            $table->dropForeign(['status_id']);
            $table->dropColumn('status_id');
            $table->dropSoftDeletes();
        });
    }
};
