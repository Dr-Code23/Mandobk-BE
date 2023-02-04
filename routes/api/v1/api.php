<?php

use Illuminate\Support\Facades\Auth;
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

    // Public Site Routes
    Route::group(['prefix' => 'site', 'namespace' => 'Site'], function () {
        Route::group(
            ['prefix' => 'company', 'namespace' => 'Company'],
            function () {
                Route::group(['prefix' => 'products'], function () {
                    Route::get('', 'ProductController@index');
                    Route::post('', 'ProductController@store');
                }
                );
                Route::group(
                    ['prefix' => 'sales'],
                    function () {
                        Route::get('', 'SalesController@index')->name('company-sales-all');
                        Route::post('', 'SalesController@store')->name('company-sales-add');
                    }
                );

                // Company Offers
                Route::group(
                    ['prefix' => 'company_offers'],
                    function () {
                        Route::get('', 'CompanyOffersController@index');
                        Route::get('{offer}', 'CompanyOffersController@show');
                        Route::post('', 'CompanyOffersController@store');
                        Route::put('/{offer}', 'CompanyOffersController@update');
                        Route::delete('/{offer}', 'CompanyOffersController@destroy');
                    }
                );
            }
        );
    });

    Route::group(['prefix' => 'mobile'], function () {});

    Route::group(['prefix' => 'dashboard'], function () {
    });
});
Route::get('/', function () {
    return Auth::user();
})->middleware(['auth:api']);
