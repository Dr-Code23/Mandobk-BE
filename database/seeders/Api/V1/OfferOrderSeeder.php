<?php

namespace Database\Seeders\Api\V1;

use App\Models\V1\OfferOrder;
use Illuminate\Database\Seeder;

class OfferOrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        for ($i = 0; $i < 50; ++$i) {
            OfferOrder::create([
                'offer_id' => fake()->numberBetween(1, 29),
                'want_offer_id' => fake()->numberBetween(1, 11),
                'qty' => fake()->numberBetween(1, 100),
                'status' => fake()->numberBetween(0, 2).'',
            ]);
        }
    }
}
