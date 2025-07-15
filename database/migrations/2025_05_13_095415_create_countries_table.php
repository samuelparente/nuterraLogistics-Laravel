<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('countries', function (Blueprint $table) {
            $table->id();

            $table->string('code')->unique();         // Ex: 'pt', 'es', 'com'
            $table->string('domain')->unique();       // Ex: 'peixeverde.pt'
            $table->string('locale');                 // Ex: 'pt', 'es', 'en'
            $table->string('name');                   // Ex: 'Portugal'
            $table->string('currency')->default('EUR');     // Ex: 'EUR', 'USD'
            $table->string('timezone')->default('UTC');     // Ex: 'Europe/Lisbon'
            $table->boolean('active')->default(true);       // Está ativa ou não

            $table->timestamps();                     // created_at, updated_at
            $table->softDeletes();                    // deleted_at (para remoção lógica)
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('countries');
    }
};
