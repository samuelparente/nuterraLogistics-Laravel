<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('stores', function (Blueprint $table) {
            $table->string('db_host', 100)->default('127.0.0.1')->after('domain');
            $table->string('db_port', 10)->default('3306')->after('db_host');
            $table->string('db_username', 100)->nullable()->after('db_port');
            $table->string('db_password', 255)->nullable()->after('db_username');
        });
    }

    public function down(): void
    {
        Schema::table('stores', function (Blueprint $table) {
            $table->dropColumn(['db_host', 'db_port', 'db_username', 'db_password']);
        });
    }
};
