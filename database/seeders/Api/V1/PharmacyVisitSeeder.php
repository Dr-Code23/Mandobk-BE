<?php

namespace Database\Seeders\Api\V1;

use App\Models\V1\PharmacyVisit;
use Illuminate\Database\Seeder;

class PharmacyVisitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($i = 0; $i < 10; $i++) {
            PharmacyVisit::create([
                'doctor_id' => 7,
                'pharmacy_id' => 9,
                'visitor_recipe_id' => fake()->numberBetween(1, 40),
            ]);
        }
    }
}
