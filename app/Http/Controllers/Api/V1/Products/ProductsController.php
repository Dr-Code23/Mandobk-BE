<?php

namespace App\Http\Controllers\Api\V1\Products;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Product\productRequest;
use App\Models\V1\Product;
use App\Models\V1\Role;
use App\RepositoryInterface\ProductRepositoryInterface;
use App\Traits\roleTrait;
use App\Traits\userTrait;
use Illuminate\Support\Facades\Auth;

class ProductsController extends Controller
{
    use userTrait;
    use roleTrait;
    private ProductRepositoryInterface $productRepository;

    public function __construct(ProductRepositoryInterface $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function index()
    {
        return $this->productRepository->showAllProducts();
    }

    public function show(Product $product)
    {
        return $this->productRepository->showOneProduct($product);
    }

    public function store(productRequest $request)
    {
        return $this->productRepository->storeProduct($request);
    }

    public function update(productRequest $request, Product $product)
    {
        return $this->productRepository->updateProduct($request, $product);
    }

    public function destroy(Product $product)
    {
        return $this->productRepository->deleteProduct($product);
    }

    public function ScientificNamesSelect()
    {
        return Product::where('user_id', Auth::id())->get(['id', 'sc_name as scientific_name']);
    }

    public function CommercialNamesSelect()
    {
        return Product::where('user_id', Auth::id())->get(['id', 'com_name as commercial_names']);
    }

    public function doctorProducts()
    {
        if ($this->getRoleNameForAuthenticatedUser() == 'doctor') {
            return Product::whereIn(
                'role_id',
                [
                    Role::where('name', 'ceo')->value('id'),
                    Role::where('name', 'data_entry')->value('id'),
                ]
            )
                ->get(['id', 'sc_name as scientific_name', 'limited']);
        }

        return $this->notFoundResponse();
    }
}
