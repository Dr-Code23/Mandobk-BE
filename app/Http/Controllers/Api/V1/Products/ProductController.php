<?php

namespace App\Http\Controllers\Api\V1\Products;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Product\ProductRequest;
use App\Http\Resources\Api\V1\Product\ProductResource;
use App\Models\V1\Product;
use App\Services\Api\V1\Products\ProductService;
use App\Traits\HttpResponse;
use App\Traits\RoleTrait;
use App\Traits\Translatable;
use App\Traits\UserTrait;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ProductController extends Controller
{
    use UserTrait, RoleTrait, Translatable, HttpResponse;

    public function __construct(
        private readonly ProductService $productService
    ) {
    }

    /**
     * Fetch All Products
     */
    public function index(): JsonResponse
    {
        return $this->productService->fetchAllProducts();
    }

    /**
     * Show Product Without Fetching `ProductInfo` Relationship
     */
    public function showWithoutDetails(Product $product): JsonResponse
    {
        $product = $this->productService->showOnProductWithoutDetails($product);

        if ($product != null) {
            return $this->resourceResponse(new ProductResource($product));
        }

        return $this->notFoundResponse($this->translateErrorMessage('product', 'not_exists'));
    }

    /**
     * Store Or Update Product For User
     */
    public function storeOrUpdate(ProductRequest $request): JsonResponse
    {
        $product = $this->productService->storeOrUpdate($request);

        if (is_bool($product) && ! $product) {
            return $this->error(
                null,
                'Failed To Store Barcode',
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        return $this->resourceResponse(new ProductResource($product));
    }

    /**
     * Delete Product
     */
    public function destroy(Product $product): JsonResponse
    {
        $productDeleted = $this->productService->destroy($product);

        if ($productDeleted) {
            return $this->success(null, $this->translateSuccessMessage('product', 'deleted'));
        } else {
            return $this->notFoundResponse($this->translateErrorMessage('product', 'not_exists'));
        }
    }

    /**
     * Fetch All Scientific Names For Select Box
     */
    public function scientificNamesSelect(ProductService $productService): JsonResponse
    {
        return $this->resourceResponse($productService->ScientificNamesSelect());
    }

    /**
     * Fetch All Commercial Names For Select Box
     */
    public function commercialNamesSelect(ProductService $productService): JsonResponse
    {
        return $this->resourceResponse($productService->CommercialNamesSelect());
    }

    /**
     * Fetch All Products For Doctor
     */
    public function doctorProducts(ProductService $productService): JsonResponse
    {
        return $this->resourceResponse($productService->doctorProducts());
    }

    /**
     * Confirm Updating Limited Exchange
     */
    public function updateLimitedExchange(int $productId, ProductService $productService): JsonResponse
    {
        $product = $productService->updateLimitedExchange($productId);

        if (is_bool($product) && ! $product) {
            return $this->notFoundResponse(
                $this->translateErrorMessage('product', 'not_found')
            );
        }

        return $this->success(
            [
                'id' => $product->id,
                'limited' => (bool) $product->limited,
            ],
            $this->translateSuccessMessage('limited', 'updated')
        );
    }
}
