<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up(): void
    {
        Schema::create('app_settings', function (Blueprint $table) {
            $table->id();

            // Notificações por email
            $table->json('notification_to')->nullable();
            $table->json('notification_cc')->nullable();

            // SMTP config
            $table->string('smtp_host')->nullable();
            $table->unsignedSmallInteger('smtp_port')->nullable();
            $table->string('smtp_user')->nullable();
            $table->string('smtp_password')->nullable();
            $table->enum('smtp_encryption', ['ssl', 'tls'])->nullable();
            $table->string('smtp_from_address')->nullable();
            $table->string('smtp_from_name')->nullable();

            $table->timestamps();
        });

        // Inserção inicial com valores do .env
        \DB::table('app_settings')->insert([
            'notification_to' => json_encode(['sofia@peixeverde.pt']),
            'notification_cc' => json_encode(['desenvolvimento@peixeverde.pt', 'fernanda@peixeverde.pt']),
            'smtp_host' => env('MAIL_HOST'),
            'smtp_port' => env('MAIL_PORT'),
            'smtp_user' => env('MAIL_USERNAME'),
            'smtp_password' => env('MAIL_PASSWORD'),
            'smtp_encryption' => env('MAIL_ENCRYPTION'),
            'smtp_from_address' => env('MAIL_FROM_ADDRESS'),
            'smtp_from_name' => env('MAIL_FROM_NAME'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('app_settings');
    }
};
