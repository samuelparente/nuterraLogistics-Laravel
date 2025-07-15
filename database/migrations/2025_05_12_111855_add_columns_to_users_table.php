<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // Verifica se a coluna 'is_active' já existe antes de adicioná-la
            if (!Schema::hasColumn('users', 'is_active')) {
                $table->boolean('is_active')->default(true); // Define se o usuário está ativo
            }

            // Verifica se a coluna 'is_verified' já existe antes de adicioná-la
            if (!Schema::hasColumn('users', 'is_verified')) {
                $table->boolean('is_verified')->default(false); // Se o email foi verificado
            }

            // Verifica se a coluna 'avatar' já existe antes de adicioná-la
            if (!Schema::hasColumn('users', 'avatar')) {
                $table->string('avatar')->nullable(); // Para o caminho do avatar (foto de perfil)
            }

            // Verifica se a coluna 'email_verified_at' já existe antes de adicioná-la
            if (!Schema::hasColumn('users', 'email_verified_at')) {
                $table->timestamp('email_verified_at')->nullable(); // Data de verificação do email
            }

            // Verifica se a coluna 'deleted_at' já existe antes de adicioná-la (para soft deletes)
            if (!Schema::hasColumn('users', 'deleted_at')) {
                $table->softDeletes(); // Adiciona a coluna deleted_at para soft deletes
            }
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            // Remove as colunas, mas verifica se elas existem antes
            if (Schema::hasColumn('users', 'is_active')) {
                $table->dropColumn('is_active');
            }
            if (Schema::hasColumn('users', 'is_verified')) {
                $table->dropColumn('is_verified');
            }
            if (Schema::hasColumn('users', 'avatar')) {
                $table->dropColumn('avatar');
            }
            if (Schema::hasColumn('users', 'email_verified_at')) {
                $table->dropColumn('email_verified_at');
            }
            if (Schema::hasColumn('users', 'deleted_at')) {
                $table->dropSoftDeletes(); // Remove a coluna deleted_at
            }
        });
    }
};
