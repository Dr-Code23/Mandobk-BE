<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use Database\Seeders\Api\Web\V1\dataEntrySeeder;
use Database\Seeders\Api\Web\V1\humanResourceSeeder;
use Database\Seeders\Api\Web\V1\RolesSeeder;
use Database\Seeders\Api\Web\V1\UserSeeder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
        $this->call([
            RolesSeeder::class,
            UserSeeder::class,
            dataEntrySeeder::class,
            humanResourceSeeder::class,
        ]);
    }
}
