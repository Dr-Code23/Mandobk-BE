<?php

namespace App\Http\Controllers\Api\V1\Products;

use App\Http\Controllers\Controller;
use App\Models\Api\V1\Product;
use App\Traits\userTrait;

class MainProductController extends Controller
{
    use userTrait;

    public function ScienteficNamesSelect()
    {
        return Product::where('user_id', $this->getAuthenticatedUserId())->get(['id', 'sc_name as scientefic_name']);
    }

    public function CommercialNamesSelect()
    {
        return Product::where('user_id', $this->getAuthenticatedUserId())->get(['id', 'com_name as commercial_names']);
    }
}
