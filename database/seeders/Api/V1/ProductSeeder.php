<?php

namespace Database\Seeders\Api\V1;

use App\Models\V1\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        // For Testing
        for ($i = 0; $i < 50; ++$i) {
            Product::create([
                'com_name' => fake()->name(),
                'sc_name' => fake()->name(),
                'pur_price' => fake()->randomFloat(1, 1, 1000),
                'sel_price' => fake()->randomFloat(1, 1, 1000),
                'bonus' => fake()->numberBetween(1, 100),
                'con' => fake()->numberBetween(1, 1000),
                'limited' => fake()->boolean(),
                'barcode' => '',
                'user_id' => fake()->numberBetween(1, 10),
                'role_id' => fake()->numberBetween(1, 10),
            ]);
        }
    }
}
