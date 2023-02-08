<?php

namespace App\Http\Controllers\Api\V1\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Product\productRequest;
use App\Models\V1\Product;
use App\RepositoryInterface\ProductRepositoryInterface;

// use App\Traits\productTrait;

class DataEntryController extends Controller
{
    private $productRepository;

    public function __construct(ProductRepositoryInterface $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function index()
    {
        return $this->productRepository->showAllProducts(Product::all());
    }

    /**
     * Store Product For Data Entry.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(productRequest $request)
    {
        return $this->productRepository->storeProduct($request);
    }

    public function show(Product $dataEntry)
    {
        return $this->productRepository->showOneProduct($dataEntry);
    }

    public function update(productRequest $productRequest, Product $dataEntry)
    {
        return $this->productRepository->updateProduct($productRequest, $dataEntry);
    }

    public function destroy(Product $dataEntry)
    {
        return $this->productRepository->deleteProduct($dataEntry);
    }
}
