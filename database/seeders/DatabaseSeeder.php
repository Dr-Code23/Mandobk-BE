<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use Database\Seeders\Api\Web\V1\companyOffersSeeder;
use Database\Seeders\Api\Web\V1\humanResourceSeeder;
use Database\Seeders\Api\Web\V1\marktingSeeder;
use Database\Seeders\Api\Web\V1\payMethodsSeeder;
use Database\Seeders\Api\Web\V1\productSeeder;
use Database\Seeders\Api\Web\V1\RolesSeeder;
use Database\Seeders\Api\Web\V1\saleDetaisSeeder;
use Database\Seeders\Api\Web\V1\saleSeeder;
use Database\Seeders\Api\Web\V1\subUserSeeder;
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
        $this->call([
            RolesSeeder::class,
            UserSeeder::class,
            subUserSeeder::class,
            productSeeder::class,
            humanResourceSeeder::class,
            payMethodsSeeder::class,
            companyOffersSeeder::class,
            saleSeeder::class,
            saleDetaisSeeder::class,
            marktingSeeder::class,
        ]);
    }
}
