<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('receiving_items', function (Blueprint $table) {
            $table->string('fx_currency', 3)->default('EUR')->after('received_qty');
            $table->float('fx_rate_to_eur')->nullable()->after('fx_currency');
            $table->timestamp('fx_rate_at')->nullable()->after('fx_rate_to_eur');
        });
    }

    public function down(): void
    {
        Schema::table('receiving_items', function (Blueprint $table) {
            $table->dropColumn(['fx_currency', 'fx_rate_to_eur', 'fx_rate_at']);
        });
    }

};
