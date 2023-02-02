<?php

namespace Database\Seeders\Api\Web\V1;

use App\Models\Api\Web\V1\SaleDetail;
use Illuminate\Database\Seeder;

class saleDetaisSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for ($i = 0; $i < 100; ++$i) {
            SaleDetail::create([
                'sale_id' => fake()->numberBetween(1, 5),
                'product_id' => fake()->numberBetween(1, 50),
                'expire_date' => fake()->date(),
                'sel_price' => fake()->randomFloat(1, 1, 100),
                'pur_price' => fake()->randomFloat(1, 1, 100),
                'qty' => fake()->numberBetween(1, 100),
                'con' => fake()->randomFloat(1, 1, 100),
            ]);
        }
    }
}
