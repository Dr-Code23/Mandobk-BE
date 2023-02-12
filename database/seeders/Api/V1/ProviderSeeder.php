<?php

namespace Database\Seeders\Api\V1;

use App\Models\V1\ProviderModel;
use Illuminate\Database\Seeder;

class ProviderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for ($i = 8; $i <= 11; ++$i) {
            ProviderModel::create([
                'name' => fake()->name(),
                'user_id' => $i,
            ]);
        }
    }
}
