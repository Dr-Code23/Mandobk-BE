<?php

namespace Database\Seeders\Api\V1;

use App\Models\Api\V1\CompanyOffer;
use Illuminate\Database\Seeder;

class companyOffersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for ($i = 0; $i < 30; ++$i) {
            CompanyOffer::create([
                'sc_name' => fake()->name(),
                'com_name' => fake()->name(),
                'expire_date' => fake()->date(),
                'offer_duration' => fake()->numberBetween(0, 2).'',
                'pay_method' => 1,
                'bonus' => fake()->randomFloat(1, 100, 200),
                'user_id' => fake()->numberBetween(1, 10),
            ]);
        }
    }
}
