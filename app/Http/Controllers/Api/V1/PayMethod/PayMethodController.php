<?php

namespace App\Http\Controllers\Api\V1\PayMethod;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\PayMethod\PayMethodCollection;
use App\Models\V1\PayMethod;
use App\Traits\HttpResponse;
use Illuminate\Http\JsonResponse;

class PayMethodController extends Controller
{
    use HttpResponse;

    /**
     * Show All Payment Methods
     *
     * @return JsonResponse
     */
    public function getAllPayMethods(): JsonResponse
    {
        return $this->ResourceResponse(new PayMethodCollection(PayMethod::all(['name', 'id'])));
    }
}
