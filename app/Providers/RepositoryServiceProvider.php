<?php

namespace App\Providers;

use App\Repository\DBOfferRepository;
use App\Repository\DBProductRepository;
use App\Repository\DBSalesRepository;
use App\RepositoryInterface\OfferRepositoryInterface;
use App\RepositoryInterface\ProductRepositoryInterface;
use App\RepositoryInterface\SalesRepositoryInterface;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        // Define All Interfaces binded to repositories
        $this->app->bind(ProductRepositoryInterface::class, DBProductRepository::class);
        $this->app->bind(OfferRepositoryInterface::class, DBOfferRepository::class);
        $this->app->bind(SalesRepositoryInterface::class, DBSalesRepository::class);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
    }
}
