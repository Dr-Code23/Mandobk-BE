<?php

namespace App\Http\Controllers\Api\V1\Site\Sales;

use App\Http\Controllers\Controller;
use App\RepositoryInterface\SalesRepositoryInterface;
use Illuminate\Http\Request;

class SalesController extends Controller
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

    public function store(Request $request)
    {
        return $this->salesRepository->storeSale($request);
    }
}
