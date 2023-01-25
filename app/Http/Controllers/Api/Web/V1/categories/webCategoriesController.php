<?php

namespace App\Http\Controllers\Api\Web\V1\categories;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Web\V1\Categories\webCategoriesRequest;
use App\Http\Resources\Api\Web\V1\Categories\webCategoriesCollection;
use App\Http\Resources\Api\Web\V1\Categories\webCategoriesResource;
use App\Models\Api\Web\V1\Category;
use App\Traits\fileOperationTrait;
use App\Traits\HttpResponse;
use Illuminate\Http\Request;

class webCategoriesController extends Controller
{
    use HttpResponse;
    use fileOperationTrait;

    public function index()
    {
        return $this->success($this->getWebTranslationFile('categories/categoriesTranslationFile.php'), 'Data Fetched Successfully');
    }

    /**
     * Retrieve All Categories.
     *
     * @return webCategoriesCollection
     */
    public function all()
    {
        return new webCategoriesCollection(Category::all());
    }

    /**
     * Store A Category.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(webCategoriesRequest $req)
    {
        $cat = [
            'com_name' => $req->commercial_name,
            'sc_name' => $req->scientefic_name,
            'qty' => $req->quantity,
            'pur_price' => $req->purchase_price,
            'sel_price' => $req->selling_price,
            'bonus' => $req->bonus,
            'created_at' => $req->entry_date,
            'expire_in' => $req->expire_date,
            'con' => $req->concentrate,
            'patch_number' => $req->patch_number,
            'provider' => $req->provider,
        ];

        // Generate A Barcode for the product

        $random_number = rand(1, 1000000000);
        while (file_exists(asset('storage/categories/'.$random_number.'.svg'))) {
            $random_number = rand(1, 1000000000);
        }
        // Store the barcode
        if ($this->storeBarCodeSVG('categories', $random_number)) {
            $cat['bar_code'] = $random_number;

            return $this->success(new webCategoriesResource(Category::create($cat)), 'Category Created Successfully');
        }

        return $this->error(null, 500, 'Something went wrong');
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Category $category)
    {
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Category $category)
    {
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Category $category)
    {
    }
}
