<?php

use App\Http\Controllers\Api\Web\V1\Auth\webAuthController;
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
                    ['prefix' => 'monitor_and_evaluation'],
                    function () {
                        Route::get('view', 'monitorAndEvaluationController@lang_content');
                        Route::get('', 'monitorAndEvaluationController@index');
                        Route::post('', 'monitorAndEvaluationController@store');
                        Route::get('/{user}', 'monitorAndEvaluationController@show');
                        Route::put('/{user}', 'monitorAndEvaluationController@update');
                        Route::delete('/{user}', 'monitorAndEvaluationController@destroy');
                    }
                );
            }
        );
    });
});
