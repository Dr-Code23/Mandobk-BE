<?php

namespace Database\Seeders\Api\V1;

use App\Models\V1\Marketing;
use Illuminate\Database\Seeder;

class MarketingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        for ($i = 0; $i < 10; ++$i) {
            Marketing::create([
                'medicine_name' => fake()->name(),
                'company_name' => fake()->name(),
                'discount' => fake()->numberBetween(0, 100),
                'img' => fake()->imageUrl(),
            ]);
        }
    }
}
