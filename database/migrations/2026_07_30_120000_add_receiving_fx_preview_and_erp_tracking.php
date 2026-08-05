<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('receivings', function (Blueprint $table) {
            $table->string('fx_currency', 3)->default('EUR')->after('files');
            $table->decimal('fx_rate_to_eur', 20, 12)->nullable()->after('fx_currency');
            $table->json('fx_preview')->nullable()->after('fx_rate_to_eur');
            $table->uuid('fx_preview_token')->nullable()->unique()->after('fx_preview');
            $table->timestamp('fx_previewed_at')->nullable()->after('fx_preview_token');

            $table->string('erp_submission_status', 20)->default('pending')->after('fx_previewed_at');
            $table->uuid('erp_submission_key')->nullable()->unique()->after('erp_submission_status');
            $table->string('erp_document_reference')->nullable()->after('erp_submission_key');
            $table->json('erp_response')->nullable()->after('erp_document_reference');
            $table->timestamp('erp_submitted_at')->nullable()->after('erp_response');
        });

        Schema::table('receiving_items', function (Blueprint $table) {
            $table->decimal('fx_rate_to_eur', 20, 12)->nullable()->change();
            $table->decimal('fx_source_unit_price', 18, 6)->nullable()->after('fx_rate_at');
            $table->decimal('fx_unit_price_eur', 18, 6)->nullable()->after('fx_source_unit_price');
        });
    }

    public function down(): void
    {
        Schema::table('receiving_items', function (Blueprint $table) {
            $table->dropColumn([
                'fx_source_unit_price',
                'fx_unit_price_eur',
            ]);
            $table->float('fx_rate_to_eur')->nullable()->change();
        });

        Schema::table('receivings', function (Blueprint $table) {
            $table->dropUnique(['fx_preview_token']);
            $table->dropUnique(['erp_submission_key']);
            $table->dropColumn([
                'fx_currency',
                'fx_rate_to_eur',
                'fx_preview',
                'fx_preview_token',
                'fx_previewed_at',
                'erp_submission_status',
                'erp_submission_key',
                'erp_document_reference',
                'erp_response',
                'erp_submitted_at',
            ]);
        });
    }
};
