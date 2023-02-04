<?php

namespace App\Http\Controllers\Api\V1\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Web\V1\Product\productRequest;
use App\Models\Api\V1\Product;
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

    public function index()
    {
        return $this->showAllProducts(Product::all());
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
