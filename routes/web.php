<?php

use App\Events\PusherEvent;
use App\Http\Controllers\Handler;
use App\Http\Controllers\TestController;
use App\Mail\TestingMail;
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


Route::get('in', [TestController::class, 'showAllOffers']);
// Route::get('Internet', [TestController::class, 'showAllOffers']);

Route::get('mail', function () {
    Mail::to('user@example.com')->send(new TestingMail());
});

Route::get('', function () {
    // Hello
    return view('welcome');
});
