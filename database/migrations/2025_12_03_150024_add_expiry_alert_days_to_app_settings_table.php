<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('app_settings', function (Blueprint $table) {
            $table->unsignedInteger('expiry_alert_days_login')
                  ->default(180)
                  ->after('notification_cc');

            $table->unsignedInteger('expiry_alert_days_email')
                  ->default(180)
                  ->after('expiry_alert_days_login');
        });
    }

    public function down(): void
    {
        Schema::table('app_settings', function (Blueprint $table) {
            $table->dropColumn('expiry_alert_days_login');
            $table->dropColumn('expiry_alert_days_email');
        });
    }
};
