<?php

namespace App\Http\Controllers\Api\V1\PayMethod;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\PayMethod\PayMethodCollection;
use App\Models\V1\PayMethod;
use App\Traits\HttpResponse;

class PayMethodController extends Controller
{
    use HttpResponse;

    /**
     * Summary of getAllPayMethods.
     *
     * @return array
     */
    public function getAllPayMethods()
    {
        return $this->ResourceResponse(new PayMethodCollection(PayMethod::all(['name', 'id'])));
    }
}
