<?php

use Illuminate\Support\Facades\Route;

Route::group(['namespace' => '\App\Http\Controllers\Api\Web\V1'], function () {
    Route::group(['namespace' => 'Auth'], function () {
        // Login
        Route::group(
            ['prefix' => 'login'],
            function () {
                Route::get('', 'webLoginController@index')->name('web-v1-login-view');
                Route::post('', 'webLoginController@login')->name('web-v1-login-user');
            }
        );
        // SignUp
        Route::group(
            ['prefix' => 'signup'],
            function () {
                Route::get('', 'webSignUpController@index')->name('web-v1-signup-view');
                Route::post('', 'webSignUpController@signup')->name('web-v1-add-user');
            }
        );
        // Logout
        Route::post('/logout', 'webLogoutController@logout')->name('web-v1-logout')
            ->middleware(['auth:api', 'isAuthenticated']);
    });

    // Roles
    Route::group(
        ['prefix' => 'roles', 'namespace' => 'Roles'],
        function () {
            Route::get('/signup_roles', 'rolesController@getSignUpRoles');
        }
    );
    Route::group(['middleware' => ['auth:api', 'isAuthenticated']], function () {
        // Route::post('/t', [webAuthController::class, 'user']);
        Route::group(
            ['prefix' => 'dashboard', 'namespace' => 'Dashboard'],
            function () {
                // Data Entry

                Route::group(
                    ['prefix' => 'data_entry', 'middleware' => ['hasDataEntryPermissions']],
                    function () {
                        Route::get('view', 'dataEntryController@lang_content');
                        Route::get('', 'dataEntryController@index');
                        Route::get('/{dataEntry}', 'dataEntryController@show');
                        Route::post('', 'dataEntryController@store');
                        Route::put('/{dataEntry}', 'dataEntryController@update');
                        Route::delete('/{dataEntry}', 'dataEntryController@destroy');
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
        // Start Working On Normal Users
        Route::group(
            ['prefix' => 'company', 'namespace' => 'Company'],
            function () {
                // Company Products
                Route::group(
                    ['prefix' => 'products'],
                    function () {
                        Route::get('', 'productController@index');
                        Route::post('', 'productController@store');
                    }
                );

                // Company Offers
                Route::group(
                    ['prefix' => 'company_offers'],
                    function () {
                        Route::get('', 'companyOffersController@index');
                        Route::get('{offer}', 'companyOffersController@show');
                        Route::post('', 'companyOffersController@store');
                        Route::put('/{offer}', 'companyOffersController@update');
                        Route::delete('/{offer}', 'companyOffersController@destroy');
                    }
                );

                // Sales
                Route::group(
                    ['prefix' => 'sales'],
                    function () {
                        Route::get('', 'salesController@index');
                    }
                );
            }
        );
    });
});
