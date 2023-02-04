<?php

namespace App\Http\Controllers\Api\Web\V1\Company;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Sales\salesRequest;
use App\RepositoryInterface\SalesRepositoryInterface;
use App\Traits\salesTrait;

class SalesController extends Controller
{
    use salesTrait;
    private $salesRepository;

    public function __construct(SalesRepositoryInterface $salesRepository)
    {
        $this->salesRepository = $salesRepository;
    }

    public function index()
    {
        return $this->salesRepository->getAllSales(1);
    }

    public function store(salesRequest $request)
    {
        return $this->salesRepository->storeSale($request);
    }
}
