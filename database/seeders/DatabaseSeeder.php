<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use Database\Seeders\Api\V1\DoctorVisitsSeeder;
use Database\Seeders\Api\V1\HumanResourceSeeder;
use Database\Seeders\Api\V1\MarketingSeeder;
use Database\Seeders\Api\V1\OfferOrderSeeder;
use Database\Seeders\Api\V1\OfferSeeder;
use Database\Seeders\Api\V1\PayMethodSeeder;
use Database\Seeders\Api\V1\PharmacyVisitSeeder;
use Database\Seeders\Api\V1\ProductInfoSeeder;
use Database\Seeders\Api\V1\ProductSeeder;
use Database\Seeders\Api\V1\RoleSeeder;
use Database\Seeders\Api\V1\SaleSeeder;
use Database\Seeders\Api\V1\SubUserSeeder;
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
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            UserSeeder::class,
            PayMethodSeeder::class,
             SubUserSeeder::class,
//            ProductSeeder::class,
//            ProductInfoSeeder::class,
            // HumanResourceSeeder::class,
            // SaleSeeder::class,
            // MarketingSeeder::class,
            // OfferSeeder::class,
            // OfferOrderSeeder::class,
//             VisitorRecipeSeeder::class,
//             DoctorVisitsSeeder::class,
//             PharmacyVisitSeeder::class,
        ]);
    }
}
