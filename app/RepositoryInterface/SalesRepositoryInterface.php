<?php

namespace App\RepositoryInterface;

interface SalesRepositoryInterface
{
    public function getAllSales(int $type);

    public function storeSale($request);
}
