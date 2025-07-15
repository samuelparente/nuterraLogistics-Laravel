<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attribute_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');   // ex: select, text, boolean, etc
            $table->string('label');  // ex: Seleção, Texto, Sim/Não, etc
            $table->timestamps();
        });

        DB::table('attribute_types')->insert([
            ['name' => 'text',        'label' => 'Texto'],
            ['name' => 'textarea',    'label' => 'Área de Texto'],
            ['name' => 'select',      'label' => 'Seleção (Dropdown)'],
            ['name' => 'multiselect', 'label' => 'Seleção Múltipla'],
            ['name' => 'checkbox',    'label' => 'Checkbox (várias opções)'],
            ['name' => 'radio',       'label' => 'Rádio (uma opção)'],
            ['name' => 'boolean',     'label' => 'Sim/Não'],
            ['name' => 'number',      'label' => 'Número'],
            ['name' => 'date',        'label' => 'Data'],
            ['name' => 'color',       'label' => 'Cor'],
            ['name' => 'file',        'label' => 'Ficheiro'],
            ['name' => 'image',       'label' => 'Imagem'],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('attribute_types');
    }
};
