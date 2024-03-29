<?php

namespace Database\Seeders\Api\V1;

use App\Models\V1\HumanResource;
use Illuminate\Database\Seeder;

class HumanResourceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($i = 1; $i <= count(config('roles.all_roles')); $i++) {
            HumanResource::create([
                'user_id' => $i,
                'date' => fake()->date(),
                'status' => fake()->numberBetween(0, 2).'',
                'attendance' => fake()->time('H:i'),
                'departure' => fake()->time('H:i'),
            ]);
        }
    }
}
