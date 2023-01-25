<?php

namespace Database\Seeders\Api\Web\V1;

use App\Models\Api\Web\V1\Category;
use Illuminate\Database\Seeder;

class categoriesSeeder extends Seeder
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
            Category::create([
                'com_name' => fake()->name(),
                'sc_name' => fake()->name(),
                'qty' => fake()->numberBetween(1, 200),
                'pur_price' => fake()->randomFloat(1, 1, 1000),
                'sel_price' => fake()->randomFloat(1, 1, 1000),
                'bonus' => fake()->randomFloat(1, 1, 1000),
                'created_at' => fake()->dateTime(),
                'expire_in' => fake()->dateTime(),
                'con' => fake()->randomFloat(),
                'patch_number' => fake()->numberBetween(1, 100),
                'qr_code' => fake()->imageUrl(),
                'provider' => fake()->name(),
            ]);
        }
    }
}
