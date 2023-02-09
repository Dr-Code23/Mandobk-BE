<?php

namespace App\Http\Controllers\Api\V1\Site\Pharmacy;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Product\productRequest;
use App\Models\V1\Product;
use App\RepositoryInterface\ProductRepositoryInterface;
use App\Traits\userTrait;

class ProductsController extends Controller
{
    use userTrait;
    private ProductRepositoryInterface $productRepository;

    public function __construct(ProductRepositoryInterface $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    /**
     * @return mixed
     */
    public function index()
    {
        return $this->productRepository->showAllProducts(
            Product::WhereIn('user_id', $this->getSubUsersForAuthenticatedUser())->get()
        );
    }

    public function store(productRequest $request)
    {
        return $this->productRepository->storeProduct($request);
    }
}
