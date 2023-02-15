<?php

namespace App\Http\Controllers\Api\V1\Site\Sales;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Sales\SaleRequest;
use App\RepositoryInterface\SalesRepositoryInterface;

class SaleController extends Controller
{
    private SalesRepositoryInterface $salesRepository;

    public function __construct(SalesRepositoryInterface $salesRepository)
    {
        $this->salesRepository = $salesRepository;
    }

    public function index()
    {
        return $this->salesRepository->getAllSales();
    }

    public function store(SaleRequest $request)
    {
        return $this->salesRepository->storeSale($request);
    }
}
