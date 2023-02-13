<?php

namespace Database\Seeders\Api\V1;

use App\Models\V1\Markting;
use Illuminate\Database\Seeder;

class MarktingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for ($i = 0; $i < 10; ++$i) {
            Markting::create([
                'medicine_name' => fake()->name(),
                'company_name' => fake()->name(),
                'discount' => fake()->numberBetween(0, 100),
                'img' => fake()->imageUrl(),
            ]);
        }
    }
}
