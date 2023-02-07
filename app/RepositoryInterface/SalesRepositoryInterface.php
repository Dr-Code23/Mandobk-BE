<?php

namespace App\RepositoryInterface;

interface SalesRepositoryInterface
{
    public function getAllSales();

    public function storeSale($request);
}
