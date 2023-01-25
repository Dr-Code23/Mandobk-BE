<?php

use App\Http\Controllers\Api\Web\V1\Auth\webAuthController;
use Illuminate\Support\Facades\Route;

Route::group(['namespace' => '\App\Http\Controllers\Api\Web\V1\Auth'], function () {
    // Login
    Route::group(['prefix' => 'login'], function () {
        Route::get('', 'webLoginController@index')->name('web-login-view');
        Route::post('', 'webLoginController@login')->name('web-login-user');
    }
    );
    // SignUp
    Route::group(['prefix' => 'signup'], function () {
        Route::get('', 'webSignUpController@index')->name('web-signup-view');
        Route::post('', 'webSignUpController@signup')->name('web-add-user');
    }
    );
    // Logout
    Route::post('/logout', 'webLogoutController@logout')->name('web-logout')
        ->middleware(['auth:api', 'isAuthenticated']);
});

// Route::group(['middleware' => ['auth:api', 'isAuthenticated']], function () {
//     Route::post('/t', [webAuthController::class, 'user']);
// });

Route::get('/loginsasdf', function () { })->name('login');
