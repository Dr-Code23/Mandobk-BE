<?php

namespace App\Http\Controllers\Api\Web\V1\Auth;

use App\Http\Controllers\Controller;
use App\Traits\HttpResponse;
use Illuminate\Support\Facades\Auth;

class webLogoutController extends Controller
{
    use HttpResponse;

    /**
     * Logout User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        Auth::logout();
        return $this->success(msg: __('standard.logged_out'));
    }
}
