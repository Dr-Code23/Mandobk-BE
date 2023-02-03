<?php

namespace App\Http\Controllers\Api\V1\Site\Company;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Site\Company\SalesRequest;
use App\RepositoryInterface\SalesRepositoryInterface;

class SalesController extends Controller
{
    private $salesRepository;

    public function __construct(SalesRepositoryInterface $salesRepository)
    {
        $this->salesRepository = $salesRepository;
    }

    public function index()
    {
        return $this->salesRepository->getAllSales(1);
    }

    public function store(SalesRequest $request)
    {
        return $this->salesRepository->storeSale($request);
    }
}
