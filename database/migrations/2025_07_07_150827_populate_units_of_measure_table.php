<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $units = [
            ['code'=>'mg',    'translations'=>[
                ['locale'=>'pt','country_id'=>620,'name'=>'Miligramas'],
                ['locale'=>'en','country_id'=>840,'name'=>'Milligrams'],
                ['locale'=>'es','country_id'=>724,'name'=>'Miligramos'],
            ]],
            ['code'=>'g',     'translations'=>[
                ['locale'=>'pt','country_id'=>620,'name'=>'Gramas'],
                ['locale'=>'en','country_id'=>840,'name'=>'Grams'],
                ['locale'=>'es','country_id'=>724,'name'=>'Gramos'],
            ]],
            ['code'=>'kg',    'translations'=>[
                ['locale'=>'pt','country_id'=>620,'name'=>'Quilogramas'],
                ['locale'=>'en','country_id'=>840,'name'=>'Kilograms'],
                ['locale'=>'es','country_id'=>724,'name'=>'Kilogramos'],
            ]],
            ['code'=>'oz',    'translations'=>[
                ['locale'=>'pt','country_id'=>620,'name'=>'Onças'],
                ['locale'=>'en','country_id'=>840,'name'=>'Ounces'],
                ['locale'=>'es','country_id'=>724,'name'=>'Onzas'],
            ]],
            ['code'=>'lb',    'translations'=>[
                ['locale'=>'pt','country_id'=>620,'name'=>'Libras'],
                ['locale'=>'en','country_id'=>840,'name'=>'Pounds'],
                ['locale'=>'es','country_id'=>724,'name'=>'Libras'],
            ]],

            // Volume
            ['code'=>'ml',    'translations'=>[
                ['locale'=>'pt','country_id'=>620,'name'=>'Mililitros'],
                ['locale'=>'en','country_id'=>840,'name'=>'Millilitres'],
                ['locale'=>'es','country_id'=>724,'name'=>'Mililitros'],
            ]],
            ['code'=>'l',     'translations'=>[
                ['locale'=>'pt','country_id'=>620,'name'=>'Litros'],
                ['locale'=>'en','country_id'=>840,'name'=>'Litres'],
                ['locale'=>'es','country_id'=>724,'name'=>'Litros'],
            ]],
            ['code'=>'floz',  'translations'=>[
                ['locale'=>'pt','country_id'=>620,'name'=>'Onças líquidas'],
                ['locale'=>'en','country_id'=>840,'name'=>'Fluid Ounces'],
                ['locale'=>'es','country_id'=>724,'name'=>'Onzas Líquidas'],
            ]],
            ['code'=>'pint',  'translations'=>[
                ['locale'=>'pt','country_id'=>620,'name'=>'Pinta'],
                ['locale'=>'en','country_id'=>840,'name'=>'Pint'],
                ['locale'=>'es','country_id'=>724,'name'=>'Pinta'],
            ]],
            ['code'=>'quart', 'translations'=>[
                ['locale'=>'pt','country_id'=>620,'name'=>'Quarto'],
                ['locale'=>'en','country_id'=>840,'name'=>'Quart'],
                ['locale'=>'es','country_id'=>724,'name'=>'Cuarto'],
            ]],
            ['code'=>'gal',   'translations'=>[
                ['locale'=>'pt','country_id'=>620,'name'=>'Galão'],
                ['locale'=>'en','country_id'=>840,'name'=>'Gallon'],
                ['locale'=>'es','country_id'=>724,'name'=>'Galón'],
            ]],

            // Comprimento
            ['code'=>'mm',    'translations'=>[
                ['locale'=>'pt','country_id'=>620,'name'=>'Milímetros'],
                ['locale'=>'en','country_id'=>840,'name'=>'Millimetres'],
                ['locale'=>'es','country_id'=>724,'name'=>'Milímetros'],
            ]],
            ['code'=>'cm',    'translations'=>[
                ['locale'=>'pt','country_id'=>620,'name'=>'Centímetros'],
                ['locale'=>'en','country_id'=>840,'name'=>'Centimetres'],
                ['locale'=>'es','country_id'=>724,'name'=>'Centímetros'],
            ]],
            ['code'=>'m',     'translations'=>[
                ['locale'=>'pt','country_id'=>620,'name'=>'Metros'],
                ['locale'=>'en','country_id'=>840,'name'=>'Metres'],
                ['locale'=>'es','country_id'=>724,'name'=>'Metros'],
            ]],
            ['code'=>'inch',  'translations'=>[
                ['locale'=>'pt','country_id'=>620,'name'=>'Polegadas'],
                ['locale'=>'en','country_id'=>840,'name'=>'Inches'],
                ['locale'=>'es','country_id'=>724,'name'=>'Pulgadas'],
            ]],
            ['code'=>'ft',    'translations'=>[
                ['locale'=>'pt','country_id'=>620,'name'=>'Pés'],
                ['locale'=>'en','country_id'=>840,'name'=>'Feet'],
                ['locale'=>'es','country_id'=>724,'name'=>'Pies'],
            ]],
            ['code'=>'yd',    'translations'=>[
                ['locale'=>'pt','country_id'=>620,'name'=>'Jardas'],
                ['locale'=>'en','country_id'=>840,'name'=>'Yards'],
                ['locale'=>'es','country_id'=>724,'name'=>'Yardas'],
            ]],

            // Unidades farmacêuticas / contagem
            ['code'=>'caps',  'translations'=>[
                ['locale'=>'pt','country_id'=>620,'name'=>'Cápsulas'],
                ['locale'=>'en','country_id'=>840,'name'=>'Capsules'],
                ['locale'=>'es','country_id'=>724,'name'=>'Cápsulas'],
            ]],
            ['code'=>'tab',   'translations'=>[
                ['locale'=>'pt','country_id'=>620,'name'=>'Comprimidos'],
                ['locale'=>'en','country_id'=>840,'name'=>'Tablets'],
                ['locale'=>'es','country_id'=>724,'name'=>'Comprimidos'],
            ]],
            ['code'=>'vial',  'translations'=>[
                ['locale'=>'pt','country_id'=>620,'name'=>'Frascos'],
                ['locale'=>'en','country_id'=>840,'name'=>'Vials'],
                ['locale'=>'es','country_id'=>724,'name'=>'Frascos'],
            ]],
            ['code'=>'bottle','translations'=>[
                ['locale'=>'pt','country_id'=>620,'name'=>'Garrafas'],
                ['locale'=>'en','country_id'=>840,'name'=>'Bottles'],
                ['locale'=>'es','country_id'=>724,'name'=>'Botellas'],
            ]],
            ['code'=>'sachet','translations'=>[
                ['locale'=>'pt','country_id'=>620,'name'=>'Saquetas'],
                ['locale'=>'en','country_id'=>840,'name'=>'Sachets'],
                ['locale'=>'es','country_id'=>724,'name'=>'Sobres'],
            ]],
            ['code'=>'dose',  'translations'=>[
                ['locale'=>'pt','country_id'=>620,'name'=>'Doses'],
                ['locale'=>'en','country_id'=>840,'name'=>'Doses'],
                ['locale'=>'es','country_id'=>724,'name'=>'Dosis'],
            ]],
            ['code'=>'drop',  'translations'=>[
                ['locale'=>'pt','country_id'=>620,'name'=>'Gotas'],
                ['locale'=>'en','country_id'=>840,'name'=>'Drops'],
                ['locale'=>'es','country_id'=>724,'name'=>'Gotas'],
            ]],
            ['code'=>'unit',  'translations'=>[
                ['locale'=>'pt','country_id'=>620,'name'=>'Unidade'],
                ['locale'=>'en','country_id'=>840,'name'=>'Unit'],
                ['locale'=>'es','country_id'=>724,'name'=>'Unidad'],
            ]],
            ['code'=>'pack',  'translations'=>[
                ['locale'=>'pt','country_id'=>620,'name'=>'Embalagem'],
                ['locale'=>'en','country_id'=>840,'name'=>'Pack'],
                ['locale'=>'es','country_id'=>724,'name'=>'Embalaje'],
            ]],
        ];

        foreach ($units as $unit) {
            $unitId = DB::table('units_of_measure')->insertGetId([
                'code' => $unit['code'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            foreach ($unit['translations'] as $tr) {
                DB::table('unit_of_measure_translations')->insert([
                    'unit_of_measure_id' => $unitId,
                    'locale'             => $tr['locale'],
                    'country_id'         => $tr['country_id'],
                    'name'               => $tr['name'],
                    'created_at'         => now(),
                    'updated_at'         => now(),
                ]);
            }
        }
    }

    public function down(): void
    {
        DB::table('unit_of_measure_translations')->delete();
        DB::table('units_of_measure')->delete();
    }
};
