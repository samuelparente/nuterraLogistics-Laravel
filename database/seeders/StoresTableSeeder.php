<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StoresTableSeeder extends Seeder
{
    public function run()
    {
        $now = Carbon::now();

        // Obter os IDs dos países da tabela countries
        $portugal = DB::table('countries')->where('code', 'PT')->value('id');
        $espanha = DB::table('countries')->where('code', 'ES')->value('id');
        $internacional = DB::table('countries')->where('code', 'COM')->value('id'); // ou o código correto para internacional

        DB::table('stores')->insert([
            [
                'country_id' => $portugal,
                'name' => 'Peixe Verde',
                'domain' => 'peixeverde.pt',
                'database' => 'nuterra_store_pt',
                'db_connection' => 'pt',
                'locale' => 'pt_PT',
                'currency' => 'EUR',
                'timezone' => 'Europe/Lisbon',
                'email' => 'info@peixeverde.pt',
                'phone' => '+351 262 060 800',
                'address' => 'Rua Dr. Fernando Correia 9ºB',
                'postal_code' => '2500-810',
                'city' => 'Caldas da Rainha',
                'state' => '',
                'country_code' => 'PT',
                'active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'country_id' => $espanha,
                'name' => 'Pzari Espanha',
                'domain' => 'pzari.es',
                'database' => 'nuterra_store_es',
                'db_connection' => 'es',
                'locale' => 'es_ES',
                'currency' => 'EUR',
                'timezone' => 'Europe/Madrid',
                'email' => 'info@pzari.es',
                'phone' => '+351 262 060 800',
                'address' => 'Rua Dr. Fernando Correia 9ºB',
                'postal_code' => '2500-810',
                'city' => 'Caldas da Rainha',
                'state' => '',
                'country_code' => 'ES',
                'active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'country_id' => $internacional,
                'name' => 'Pzari Internacional',
                'domain' => 'pzari.com',
                'database' => 'nuterra_store_com',
                'db_connection' => 'com',
                'locale' => 'en_US',
                'currency' => 'USD',
                'timezone' => 'America/New_York',
                'email' => 'info@pzari.com',
                'phone' => '+351 262 060 800',
                'address' => 'Rua Dr. Fernando Correia 9ºB',
                'postal_code' => '2500-810',
                'city' => 'Caldas da Rainha',
                'state' => '',
                'country_code' => 'US',
                'active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }
}
