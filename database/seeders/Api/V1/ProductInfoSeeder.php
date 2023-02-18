<?php

namespace Database\Seeders\Api\V1;


use Illuminate\Database\Seeder;
use App\Models\V1\ProductInfo;

class ProductInfoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for ($i = 0; $i < 100; $i++) {
            ProductInfo::create([
                'role_id' => fake()->numberBetween(1, 10),
                'product_id' => fake()->numberBetween(1, 49),
                'qty' => fake()->numberBetween(1, 100),
                'expire_date' => fake()->date(),
                'patch_number' => fake()->numberBetween(1, 100)
            ]);
        }
    }
}
