<?php

use App\Events\PusherEvent;
use App\Http\Controllers\Handler;
use App\Http\Controllers\TestController;
use BeyondCode\LaravelWebSockets\Facades\WebSocketsRouter;
use Illuminate\Support\Facades\Route;

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


Route::get('in', [TestController::class, 'index']);
// Route::get('Internet', [TestController::class, 'index']);
