<?php

namespace Database\Seeders\Api\V1;

use App\Models\V1\VisitorRecipe;
use App\Traits\UserTrait;
use Illuminate\Database\Seeder;

class VisitorRecipeSeeder extends Seeder
{
    use UserTrait;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for ($i = 1; $i <= 50; ++$i) {
            VisitorRecipe::create([
                'visitor_id' => 12,
                'random_number' => $this->generateRandomNumberForVisitor(),
                'details' => [[
                'scientific_name' => fake()->name(),
                'commercial_name' => fake()->name(),
                'concentrate' => fake()->numberBetween(1, 100),
                'taken' => fake()->boolean(),
                ]],
                'alias' => fake()->name(),
            ]);
        }
    }
}
