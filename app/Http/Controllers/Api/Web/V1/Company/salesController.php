<?php

namespace App\Http\Controllers\Api\Web\V1\Company;

use App\Http\Controllers\Controller;
use App\Traits\salesTrait;

class salesController extends Controller
{
    use salesTrait;

    public function index()
    {
        return $this->getAllSales();
    }
}
