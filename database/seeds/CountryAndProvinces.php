<?php

use Illuminate\Database\Seeder;

class CountryAndProvinces extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \Illuminate\Support\Facades\DB::table('provinces')->delete();
        \Illuminate\Support\Facades\DB::table('countries')->delete();

        \Illuminate\Support\Facades\DB::table('countries')->insert([
            [
                'code' => 'ca',
                'name' => 'Canada',
                'name_translations' => json_encode(['fr' => 'Canada']),
            ],
//            [
//                'code' => 'us',
//                'name' => 'United States of America',
//                'name_translations' => json_encode(['fr' => 'les États-Unis d\'Amérique']),
//            ]
        ]);

        \Illuminate\Support\Facades\DB::table('provinces')->insert([
            [
                'code' => 'ca-ab',
                'country_code' => 'ca',
                'name' => 'Alberta',
                'name_translations' => json_encode(['fr' => 'Alberta']),
            ],
            [
                'code' => 'ca-bc',
                'country_code' => 'ca',
                'name' => 'British Columbia',
                'name_translations' => json_encode(['fr' => 'Colombie-Britannique']),
            ],
            [
                'code' => 'ca-mb',
                'country_code' => 'ca',
                'name' => 'Manitoba',
                'name_translations' => json_encode(['fr' => 'Manitoba']),
            ],
            [
                'code' => 'ca-nb',
                'country_code' => 'ca',
                'name' => 'New Brunswick',
                'name_translations' => json_encode(['fr' => 'Nouveau-Brunswick']),
            ],
            [
                'code' => 'ca-nl',
                'country_code' => 'ca',
                'name' => 'Newfoundland and Labrador',
                'name_translations' => json_encode(['fr' => 'Terre-Neuve-et-Labrador']),
            ],
            [
                'code' => 'ca-ns',
                'country_code' => 'ca',
                'name' => 'Nova Scotia',
                'name_translations' => json_encode(['fr' => 'Nouvelle-Écosse']),
            ],
            [
                'code' => 'ca-on',
                'country_code' => 'ca',
                'name' => 'Ontario',
                'name_translations' => json_encode(['fr' => 'Ontario']),
            ],
            [
                'code' => 'ca-pe',
                'country_code' => 'ca',
                'name' => 'Prince Edward Island',
                'name_translations' => json_encode(['fr' => 'Île-du-Prince-Édouard']),
            ],
            [
                'code' => 'ca-qc',
                'country_code' => 'ca',
                'name' => 'Quebec',
                'name_translations' => json_encode(['fr' => 'Québec']),
            ],
            [
                'code' => 'ca-sk',
                'country_code' => 'ca',
                'name' => 'Saskatchewan',
                'name_translations' => json_encode(['fr' => 'Saskatchewan']),
            ],
            [
                'code' => 'ca-nt',
                'country_code' => 'ca',
                'name' => 'Northwest Territories',
                'name_translations' => json_encode(['fr' => 'Territoires du Nord-Ouest']),
            ],
            [
                'code' => 'ca-nu',
                'country_code' => 'ca',
                'name' => 'Nunavut',
                'name_translations' => json_encode(['fr' => 'Nunavut']),
            ],
            [
                'code' => 'ca-yt',
                'country_code' => 'ca',
                'name' => 'Yukon',
                'name_translations' => json_encode(['fr' => 'Yukon']),
            ],
        ]);
    }
}
