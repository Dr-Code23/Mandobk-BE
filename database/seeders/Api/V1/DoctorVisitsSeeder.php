<?php

namespace Database\Seeders\Api\V1;

use App\Models\V1\DoctorVisit;
use Illuminate\Database\Seeder;

class DoctorVisitsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        for ($i = 0; $i < 10; ++$i) {
            DoctorVisit::create([
                'visitor_recipe_id' => fake()->numberBetween(1, 40),
                'doctor_id' => 7,
            ]);
        }
    }
}
