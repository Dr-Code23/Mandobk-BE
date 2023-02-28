<?php

namespace App\Http\Controllers\Api\V1\Site\Home;

use App\Actions\GetHomeInfoAction;
use App\Http\Controllers\Controller;
use App\Traits\HttpResponse;
use Illuminate\Http\JsonResponse;

class HomeController extends Controller
{
    use HttpResponse;

    /**
     * Get Home Page Statistics To Show
     * @param GetHomeInfoAction $homeInfoAction
     * @return JsonResponse
     */
    public function index(GetHomeInfoAction $homeInfoAction): JsonResponse
    {
        return $this->resourceResponse($homeInfoAction->getInfo());
    }
}
