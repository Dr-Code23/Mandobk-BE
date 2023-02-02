<?php

namespace App\Http\Controllers\Api\Web\V1\Company;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Web\V1\Product\productRequest;
use App\Models\Api\Web\V1\Product;
use App\Traits\productTrait;
use App\Traits\userTrait;

class productController extends Controller
{
    use productTrait;
    use userTrait;

    /**
     * List All Products For Data Entry.
     *
     * @return array
     */
    public function index()
    {
        return $this->showAllProducts(Product::where('user_id', $this->getAuthenticatedUserId())->get());
    }

    /**
     * Summary of lang_content.
     *
     * @return \App\Http\Resources\Api\Web\V1\Translation\translationResource
     */
    public function lang_content()
    {
        return $this->translateResource('Dashboard/dataEntryTranslationFile');
    }

    /**
     * Store Product For Data Entry.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(productRequest $request)
    {
        return $this->storeProduct($request);
    }

    /**
     * Update Product For Company.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(productRequest $request, Product $product)
    {
        return $this->updateProduct($request, $product);
    }

    /**
     * Delete Product For Company.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Product $product)
    {
        return $this->deleteProduct($product);
    }
}
