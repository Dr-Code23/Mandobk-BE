<?php

namespace App\Http\Controllers\Api\Web\V1\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Web\V1\Product\productRequest;
use App\Models\Api\V1\Product;
use App\RepositoryInterface\ProductRepositoryInterface;
use App\Traits\productTrait;

// use App\Traits\productTrait;

class dataEntryController extends Controller
{
    use productTrait;

    /**
     * List All Products For Data Entry.
     *
     * @return array
     */
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

    public function show()
    {
        // Feature Update
    }

    public function update()
    {
        // Feature Update
    }

    public function destroy()
    {
        // Feature Update
    }
}
