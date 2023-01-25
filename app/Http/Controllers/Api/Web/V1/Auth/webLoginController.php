<?php

namespace App\Http\Controllers\Api\Web\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\Web\V1\Auth\Login\webLoginResource;

class webLoginController extends Controller
{
    public function index()
    {
        return new webLoginResource(['name' => 'Google']);
    }
}
