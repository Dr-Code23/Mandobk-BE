<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::group(['namespace' => '\App\Http\Controllers\Api\Web\V1'], function () {
    Route::post('/signup', 'authController@signup')->name('add-user');
});

Route::get('/', function () {
    return Auth::user();
})->middleware('auth');
