<?php

namespace App\Http\Controllers\Api\V1\Site\Storehouse;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Product\productRequest;
use App\Models\V1\Product;
use App\RepositoryInterface\ProductRepositoryInterface;
use App\Traits\userTrait;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    use userTrait;
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
        return $this->productRepository->showAllProducts(Product::where('user_id', Auth::id())->get());
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
