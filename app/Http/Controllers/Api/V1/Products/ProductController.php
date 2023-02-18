<?php

namespace App\Http\Controllers\Api\V1\Products;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Product\ProductRequest;
use App\Models\V1\Product;
use App\RepositoryInterface\ProductRepositoryInterface;
use App\Traits\RoleTrait;
use App\Traits\UserTrait;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    use UserTrait;
    use RoleTrait;
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
        return $this->productRepository->showOneProductWithDetails($product);
    }

    public function store(ProductRequest $request)
    {
        return $this->productRepository->storeProduct($request);
    }

    public function update(ProductRequest $request, Product $product)
    {
        return $this->productRepository->updateProduct($request, $product);
    }

    public function destroy(Product $product)
    {
        return $this->productRepository->deleteProduct($product);
    }

    public function ScientificNamesSelect()
    {
        return $this
            ->resourceResponse(Product::where('user_id', Auth::id())
                ->get(['id', 'sc_name as scientific_name']));
    }

    public function CommercialNamesSelect()
    {
        return $this->resourceResponse(Product::where('user_id', Auth::id())->get(['id', 'com_name as commercial_names']));
    }

    public function doctorProducts()
    {
        if ($this->getRoleNameForAuthenticatedUser() == 'doctor') {
            return $this->resourceResponse(Product::whereIn(
                'role_id',
                $this->getRolesIdsByName(['ceo', 'data_entry']),
            )
                ->get(['id', 'sc_name as scientific_name', 'limited']));
        }

        return $this->notFoundResponse();
    }
}
