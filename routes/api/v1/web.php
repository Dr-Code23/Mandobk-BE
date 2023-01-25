<?php

use App\Http\Controllers\Api\Web\V1\Auth\webAuthController;
use Illuminate\Support\Facades\Route;

Route::group(['namespace' => '\App\Http\Controllers\Api\Web\V1\Auth'], function () {
    Route::post('/signup', 'webAuthController@signup')->name('add-user');
    Route::post('/login', 'webAuthController@login')->name('login-user');
    Route::get('/login', 'webLoginController@index');
    Route::post('/logout', 'webAuthController@logout')->name('web-logout')
        ->middleware(['auth:api', 'isAuthenticated']);
});

Route::group(['middleware' => ['auth:api', 'isAuthenticated']], function () {
    Route::post('/t', [webAuthController::class, 'user']);
});

Route::get('/loginsasdf', function () { })->name('login');
