<?php

namespace Database\Seeders\Api\V1;

use App\Models\Api\V1\Offer;
use Illuminate\Database\Seeder;

class OfferSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for ($i = 0; $i < 30; ++$i) {
            Offer::create([
                'product_id' => fake()->numberBetween(1, 40),
                'offer_duration' => fake()->numberBetween(0, 2).'',
                'pay_method' => 1,
                'bonus' => fake()->randomFloat(1, 100, 200),
                'user_id' => fake()->numberBetween(1, 10),
                'type' => fake()->numberBetween(1, 2).'',
            ]);
        }
    }
}
