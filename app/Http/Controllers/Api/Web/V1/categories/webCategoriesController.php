<?php

namespace App\Http\Controllers\Api\Web\V1\categories;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Web\V1\Categories\webCategoriesRequest;
use App\Http\Resources\Api\Web\V1\Categories\webCategoriesCollection;
use App\Models\Api\Web\V1\Category;
use App\Traits\HttpResponse;
use Illuminate\Http\Request;

class webCategoriesController extends Controller
{
    use HttpResponse;

    /**
     * Display a listing of the resource.
     *
     * @return webCategoriesCollection
     */
    public function index()
    {
        return new webCategoriesCollection(Category::all());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(webCategoriesRequest $req)
    {
        $cat = Category::create([
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
            'qr_code' => 'T',
            'provider' => $req->provider,
        ]);

        return $this->success($cat, 'Category Created Successfully');
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
