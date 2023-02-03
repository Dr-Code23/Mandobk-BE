<?php

use Illuminate\Support\Facades\Route;

Route::group(['namespace' => 'App\Http\Controllers\Api\V1'], function () {
    Route::group(['prefix' => 'auth', 'namespace' => 'Auth'], function () {
        // Login
        Route::post('/login', 'AuthController@login')->name('web-v1-login-user');
        // Signup
        Route::post('/signup', 'AuthController@signup')->name('web-v1-add-user');

        // Logout
        Route::post('/logout', 'AuthController@logout')->name('web-v1-logout')
            ->middleware(['auth:api', 'isAuthenticated']);
    });

    Route::group(['namespace' => 'Auth'], function () {
    });
    // Public Site Routes
    Route::group(['prefix' => 'site'], function () {
    });

    Route::group(['prefix' => 'mobile'], function () {});

    Route::group(['prefix' => 'dashboard'], function () {
    });
});
