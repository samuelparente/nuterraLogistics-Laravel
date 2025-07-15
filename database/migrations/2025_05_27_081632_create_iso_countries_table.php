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
        Schema::create('iso_countries', function (Blueprint $table) {
            $table->id(); // ID interno gerado pelo MySQL

            $table->integer('iso_numeric')->unique(); // ISO 3166-1 numérico (ex: 620 para Portugal)
            $table->char('iso_alpha2', 2)->unique(); // ISO Alpha-2 (PT, ES, US)
            $table->char('iso_alpha3', 3)->unique(); // ISO Alpha-3 (PRT, ESP, USA)

            $table->string('name_pt');
            $table->string('name_es');
            $table->string('name_en');
            $table->string('name_fr');
            $table->string('name_de');
            $table->string('name_it');
            $table->string('name_nl');
            $table->string('name_pl');
            $table->string('name_zh')->nullable();

            $table->string('continent_pt')->nullable();
            $table->string('continent_es')->nullable();
            $table->string('continent_en')->nullable();
            $table->string('continent_fr')->nullable();
            $table->string('continent_de')->nullable();
            $table->string('continent_it')->nullable();
            $table->string('continent_nl')->nullable();
            $table->string('continent_pl')->nullable();
            $table->string('continent_zh')->nullable();

            $table->string('region_pt')->nullable();
            $table->string('region_es')->nullable();
            $table->string('region_en')->nullable();
            $table->string('region_fr')->nullable();
            $table->string('region_de')->nullable();
            $table->string('region_it')->nullable();
            $table->string('region_nl')->nullable();
            $table->string('region_pl')->nullable();
            $table->string('region_zh')->nullable();

            $table->string('dialing_code')->nullable();
            $table->char('currency_code', 3)->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('iso_countries');
    }
};
