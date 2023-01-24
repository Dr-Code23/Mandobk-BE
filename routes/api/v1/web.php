<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::group(['namespace' => '\App\Http\Controllers\Api\Web\V1'], function () {
    Route::post('/signup', 'webAuthController@signup')->name('add-user');
    Route::post('/login', 'webAuthController@login')->name('login-user');
});
