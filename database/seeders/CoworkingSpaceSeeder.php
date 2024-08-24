<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CoworkingSpaces;

class CoworkingSpaceSeeder extends Seeder
{
    public function run()
    {
        CoworkingSpaces::create([
            'name' => 'Coworking Space 1',
            'address' => '123 Main Street',
            'city' => 'City A',
            'country' => 'Country X',
            'description' => 'A vibrant coworking space in City A.',
            'price_per_hour' => 15.00,
        ]);

        CoworkingSpaces::create([
            'name' => 'Coworking Space 2',
            'address' => '456 Elm Street',
            'city' => 'City B',
            'country' => 'Country Y',
            'description' => 'A modern coworking space in City B.',
            'price_per_hour' => 20.00,
        ]);

        CoworkingSpaces::create([
            'name' => 'Coworking Space 3',
            'address' => '789 Oak Avenue',
            'city' => 'City C',
            'country' => 'Country Z',
            'description' => 'A cozy coworking space in City C.',
            'price_per_hour' => 10.00,
        ]);
    }
}
