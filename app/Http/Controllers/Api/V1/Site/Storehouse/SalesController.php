<?php

namespace App\Http\Controllers\Api\V1\Site\Storehouse;

use App\Http\Controllers\Controller;
use App\RepositoryInterface\SalesRepositoryInterface;
use Symfony\Component\HttpFoundation\Request as HttpFoundationRequest;

class SalesController extends Controller
{
    private $salesRepository;

    public function __construct(SalesRepositoryInterface $salesRepository)
    {
        $this->salesRepository = $salesRepository;
    }

    public function index()
    {
        return $this->salesRepository->getAllSales(2);
    }

    public function store(HttpFoundationRequest $request)
    {
        return $this->salesRepository->storeSale($request);
    }
}
