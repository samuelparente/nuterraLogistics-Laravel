<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Admin\Country; 

class CountriesTableSeeder extends Seeder
{
    public function run()
    {
        $countries = [
            [
                'code' => 'pt',
                'domain' => 'peixeverde.pt',
                'locale' => 'pt_PT',
                'name' => 'Portugal',
                'currency' => 'EUR',
                'timezone' => 'Europe/Lisbon',
                'active' => true,
            ],
            [
                'code' => 'es',
                'domain' => 'pzari.es',
                'locale' => 'es_ES',
                'name' => 'Espanha',
                'currency' => 'EUR',
                'timezone' => 'Europe/Madrid',
                'active' => true,
            ],
            [
                'code' => 'com',
                'domain' => 'pzari.com',
                'locale' => 'en_US',
                'name' => 'Internacional',
                'currency' => 'USD',
                'timezone' => 'America/New_York',
                'active' => true,
            ],
        ];

        foreach ($countries as $country) {
            Country::updateOrCreate(['code' => $country['code']], $country);
        }
    }
}
