<?php

namespace Database\Seeders\Api\V1;

use App\Models\V1\Product;
use Illuminate\Database\Seeder;

class productSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // For Testing
        for ($i = 0; $i < 50; ++$i) {
            Product::create([
                'com_name' => fake()->name(),
                'sc_name' => fake()->name(),
                'qty' => fake()->numberBetween(1, 200),
                'pur_price' => fake()->randomFloat(1, 1, 1000),
                'sel_price' => fake()->randomFloat(1, 1, 1000),
                'bonus' => fake()->randomFloat(1, 1, 1000),
                'created_at' => fake()->date(),
                'expire_date' => date('Y-m-d', strtotime('+'.fake()->numberBetween(1, 100).'days')),
                'con' => fake()->randomFloat(),
                'limited' => fake()->boolean(),
                'patch_number' => fake()->numberBetween(1, 100),
                'barcode' => '',
                'user_id' => fake()->numberBetween(1, 10),
                'role_id' => fake()->numberBetween(1, 10),
                'provider_id' => fake()->numberBetween(1, 99),
            ]);
        }
    }
}
