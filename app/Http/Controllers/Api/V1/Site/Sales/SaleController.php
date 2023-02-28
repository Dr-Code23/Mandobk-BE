<?php

namespace App\Http\Controllers\Api\V1\Site\Sales;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Sales\SaleRequest;
use App\RepositoryInterface\SalesRepositoryInterface;

class SaleController extends Controller
{
    protected SalesRepositoryInterface $salesRepository;

    /**
     * @param SalesRepositoryInterface $salesRepository
     */
    public function __construct(SalesRepositoryInterface $salesRepository)
    {
        $this->salesRepository = $salesRepository;
    }

    /**
     * @return mixed
     */
    public function index(): mixed
    {
        return $this->salesRepository->getAllSales();
    }

    /**
     * @param SaleRequest $request
     * @return mixed
     */
    public function store(SaleRequest $request): mixed
    {
        return $this->salesRepository->storeSale($request);
    }
}
