<?php

use App\Events\PusherEvent;
use App\Http\Controllers\Api\V1\Roles\RoleController;
use App\Http\Controllers\Handler;
use App\Mail\TestingMail;
use App\Models\User;
use BeyondCode\LaravelWebSockets\Facades\WebSocketsRouter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Mail;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/



Route::get('mail', function () {
    Mail::to('user@example.com')->send(new TestingMail());
});

route::view('', 'welcome');
