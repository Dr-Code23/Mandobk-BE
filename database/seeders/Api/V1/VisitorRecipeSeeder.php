<?php

namespace Database\Seeders\Api\V1;

use App\Models\V1\VisitorRecipe;
use App\Traits\userTrait;
use Illuminate\Database\Seeder;

class VisitorRecipeSeeder extends Seeder
{
    use userTrait;
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
                    'id' => $i,
                    'name' => fake()->name(),
                    'limited'  => fake()->boolean()
                ]],
                'alias' => fake()->name()
            ]);
        }
    }
}
