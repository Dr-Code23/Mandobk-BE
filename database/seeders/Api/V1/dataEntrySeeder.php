<?php

namespace Database\Seeders\Api\V1;

use App\Models\V1\DataEntry;
use Illuminate\Database\Seeder;

class dataEntrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // For Testing
        for ($i = 0; $i < 0; ++$i) {
            DataEntry::create([
                'com_name' => fake()->name(),
                'sc_name' => fake()->name(),
                'qty' => fake()->numberBetween(1, 200),
                'pur_price' => fake()->randomFloat(1, 1, 1000),
                'sel_price' => fake()->randomFloat(1, 1, 1000),
                'bonus' => fake()->randomFloat(1, 1, 1000),
                'created_at' => fake()->dateTime(),
                'expire_date' => fake()->dateTime(),
                'con' => fake()->randomFloat(),
                'limited' => fake()->boolean(),
                'patch_number' => fake()->numberBetween(1, 100),
                'barcode' => '',
                'provider' => fake()->name(),
            ]);
        }
    }
}
