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

    public function index(ProductService $productService)
    {
        return $productService->fetchAllProducts();
    }

    public function showWithoutDetails(Product $product, ProductService $productService)
    {
        $product = $productService->showOnProductWithoutDetails($product);
        if ($product != null)
            return $this->resourceResponse(new ProductResource($product));

        return $this->notFoundResponse($this->translateErrorMessage('product', 'not_exists'));
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

    public function destroy(Product $product)
    {
        return $this->productRepository->deleteProduct($product);
    }

    public function ScientificNamesSelect(ProductService $productService)
    {
        return $this->resourceResponse($productService->ScientificNamesSelect());
    }

    public function CommercialNamesSelect(ProductService $productService)
    {
        return $this->resourceResponse($productService->CommercialNamesSelect());
    }

    public function doctorProducts(ProductService $productService)
    {
        return $this->resourceResponse($productService->doctorProducts());
    }
}
