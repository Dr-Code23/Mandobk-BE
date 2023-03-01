<?php

namespace Database\Seeders\Api\V1;

use App\Models\V1\Offer;
use Illuminate\Database\Seeder;

class OfferSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        for ($i = 0; $i < 30; ++$i) {
            Offer::create([
                'product_id' => fake()->numberBetween(1, 40),
                'from' => now(),
                'to' => date('Y-m-d', strtotime('+ ' . fake()->numberBetween(1, 10) . 'days')),
                'pay_method' => 1,
                'user_id' => 8,
                'type' => fake()->numberBetween(1, 2) . '',
                'status' => fake()->boolean()
            ]);
        }
    }
}
