<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use Database\Seeders\Api\V1\DoctorVisitsSeeder;
use Database\Seeders\Api\V1\humanResourceSeeder;
use Database\Seeders\Api\V1\marktingSeeder;
use Database\Seeders\Api\V1\OfferOrderSeeder;
use Database\Seeders\Api\V1\OfferSeeder;
use Database\Seeders\Api\V1\payMethodsSeeder;
use Database\Seeders\Api\V1\PharmacyVisitsSeeder;
use Database\Seeders\Api\V1\productSeeder;
use Database\Seeders\Api\V1\ProviderSeeder;
use Database\Seeders\Api\V1\RolesSeeder;
use Database\Seeders\Api\V1\saleSeeder;
use Database\Seeders\Api\V1\subUserSeeder;
use Database\Seeders\Api\V1\UserSeeder;
use Database\Seeders\Api\V1\VisitorRecipeSeeder;
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
            ProviderSeeder::class,
            subUserSeeder::class,
            productSeeder::class,
            humanResourceSeeder::class,
            payMethodsSeeder::class,
            saleSeeder::class,
            marktingSeeder::class,
            OfferSeeder::class,
            OfferOrderSeeder::class,
            VisitorRecipeSeeder::class,
            DoctorVisitsSeeder::class,
            PharmacyVisitsSeeder::class,
        ]);
    }
}
