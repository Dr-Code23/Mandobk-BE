<?php

namespace App\Http\Controllers\Api\V1\Products;

use App\Http\Controllers\Controller;
use App\Models\V1\Product;
use App\Traits\userTrait;
use Illuminate\Support\Facades\Auth;

class MainProductController extends Controller
{
    use userTrait;

    public function ScienteficNamesSelect()
    {
        return Product::where('user_id', Auth::id())->get(['id', 'sc_name as scientific_name']);
    }

    public function CommercialNamesSelect()
    {
        return Product::where('user_id', $this->getAuthenticatedUserId())->get(['id', 'com_name as commercial_names']);
    }
}
