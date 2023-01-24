<?php

use Illuminate\Support\Facades\Route;

Route::group(['namespace' => '\App\Http\Controllers\Api\Web\V1\Auth'], function () {
    Route::post('/signup', 'webAuthController@signup')->name('add-user');
    Route::post('/login', 'webAuthController@login')->name('login-user');
    Route::post('/logout', 'webAuthController@logout')->name('web-logout')
        ->middleware('auth:api');
});

Route::group(['middleware' => 'auth:api'], function () {
});
