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

    Route::group(['middleware' => ['auth:api']], function () {
        // Public Site Routes
        Route::group(['prefix' => 'site', 'namespace' => 'Site'], function () {
            // Company
            Route::group(
                ['prefix' => 'company', 'namespace' => 'Company', 'middleware' => 'hasCompanyPermissions'],
                function () {
                    // Products
                    Route::group(['prefix' => 'products'], function () {
                        Route::get('', 'ProductController@index');
                        Route::post('', 'ProductController@store');
                    }
                    );
                    // Sales
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

            // Storehouse
            Route::group(['prefix' => 'storehouse', 'namespace' => 'Storehouse'], function () {
                Route::group(
                    ['prefix' => 'products'],
                    function () {
                        Route::get('', 'ProductController@index');
                        Route::post('', 'ProductController@store');
                    }
                );
            });
        });

        Route::group(['prefix' => 'mobile'], function () {});

        Route::group(
            ['prefix' => 'dashboard', 'namespace' => 'Dashboard'],
            function () {
                // Data Entry

                Route::group(
                    ['prefix' => 'data_entry', 'middleware' => ['hasDataEntryPermissions']],
                    function () {
                        Route::get('', 'DataEntryController@index');
                        Route::get('/{dataEntry}', 'DataEntryController@show');
                        Route::post('', 'DataEntryController@store');
                        Route::put('/{dataEntry}', 'DataEntryController@update');
                        Route::delete('/{dataEntry}', 'DataEntryController@destroy');
                    }
                );

                // Monitor And Evaluation

                Route::group(
                    ['prefix' => 'monitor_and_evaluation', 'middleware' => ['hasMonitorAndEvaluationPermissions']],
                    function () {
                        Route::get('view', 'monitorAndEvaluationController@lang_content');
                        Route::get('', 'monitorAndEvaluationController@index');
                        Route::post('', 'monitorAndEvaluationController@store');
                        Route::get('/{user}', 'monitorAndEvaluationController@show');
                        Route::put('/{user}', 'monitorAndEvaluationController@update');
                        Route::delete('/{user}', 'monitorAndEvaluationController@destroy');
                    }
                );

                // Human Resources

                Route::group(
                    ['prefix' => 'human_resources', 'middleware' => ['hasHumanResourcePermissions']],
                    function () {
                        Route::get('', 'humanResourceController@index')->name('human_resource_all');
                        Route::get('{user}', 'humanResourceController@show')->name('human_resource_one');
                        Route::match(['POST', 'PUT'], '', 'humanResourceController@storeOrUpdate')->name('human_resource_store');
                    }
                );

                // Markting

                Route::group(
                    ['prefix' => 'markting', 'middleware' => ['hasMarktingPermissions']],
                    function () {
                        Route::get('', 'marktingController@index');
                        Route::get('{ad}', 'marktingController@show');
                        Route::post('', 'marktingController@store')->name('markting_store');
                        Route::post('{ad}', 'marktingController@update')->name('markting_update');
                        Route::delete('{ad}', 'marktingController@destroy');
                    }
                );

                Route::group(['prefix' => 'order_management'], function () {
                }
                );
            }
        );
    });
}
);
Route::get('/', function () {
    return Auth::user();
})->middleware(['auth:api']);
