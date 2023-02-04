<?php

namespace App\Http\Controllers\Api\V1\Products;

use App\Http\Controllers\Controller;
use App\RepositoryInterface\ProductRepositoryInterface;

class MainProductController extends Controller
{
    private $productRepository;

    public function __construct(ProductRepositoryInterface $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function ScienteficNamesSelect()
    {
        return $this->productRepository->getAllScienteficNamesInSelect();
    }
}
