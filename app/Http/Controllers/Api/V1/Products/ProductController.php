<?php

namespace App\Http\Controllers\Api\V1\Products;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Product\ProductRequest;
use App\Http\Resources\Api\V1\Product\ProductResource;
use App\Models\V1\Product;
use App\RepositoryInterface\ProductRepositoryInterface;
use App\Services\Api\V1\Products\ProductService;
use App\Traits\HttpResponse;
use App\Traits\RoleTrait;
use App\Traits\Translatable;
use App\Traits\UserTrait;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ProductController extends Controller
{
    use UserTrait;
    use RoleTrait;
    use Translatable;
    use HttpResponse;
    private ProductRepositoryInterface $productRepository;

    public function __construct(ProductRepositoryInterface $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function index()
    {
        return $this->productRepository->showAllProducts();
    }

    public function showWithoutDetails(Product $product, ProductService $productService)
    {
        $product = $productService->showOnProductWithoutDetails($product);
        if ($product != null)
            return $this->resourceResponse(new ProductResource($product));

        return $this->notFoundResponse($this->translateErrorMessage('product', 'not_exists'));
    }
    public function show(Product $product)
    {
        return $this->productRepository->showOneProductWithDetails($product);
    }

    public function storeOrUpdate(ProductRequest $request, ProductService $productService)
    {
        $product = $productService->storeOrUpdate($request);

        if (is_bool($product) && $product == false) return $this->error(
            null,
            'Failed To Store Barcode',
            Response::HTTP_INTERNAL_SERVER_ERROR
        );

        return $this->resourceResponse(new ProductResource($product));
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
