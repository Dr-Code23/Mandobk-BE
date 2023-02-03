<?php

namespace App\Providers;

use App\Repository\DBCompanyOffersRepository;
use App\Repository\DBProductRepository;
use App\RepositoryInterface\CompanyOffersRepositoryInterface;
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
        $this->app->bind(CompanyOffersRepositoryInterface::class, DBCompanyOffersRepository::class);
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
