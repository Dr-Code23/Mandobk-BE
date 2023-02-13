<?php

namespace Database\Seeders\Api\V1;

use App\Models\Api\V1\PharmacyVisits;
use App\Models\V1\PharmacyVisit;
use Illuminate\Database\Seeder;

class PharmacyVisitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for ($i = 0; $i < 10; $i++) {
            PharmacyVisit::create([
                'doctor_id' => 7,
                'pharmacy_id' => 9,
                'visitor_recipe_id' => fake()->numberBetween(1, 40)
            ]);
        }
    }
}
