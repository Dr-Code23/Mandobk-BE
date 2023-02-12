<?php

use App\Http\Controllers\Api\V1\Archive\ArchiveController;
use App\Http\Controllers\Api\V1\Auth\AuthController;
use App\Http\Controllers\Api\V1\Dashboard\HumanResourceController;
use App\Http\Controllers\Api\V1\Dashboard\MarktingController;
use App\Http\Controllers\Api\V1\Dashboard\MonitorAndEvaluationController;
use App\Http\Controllers\Api\V1\Dashboard\OrderManagementController;
use App\Http\Controllers\Api\V1\PayMethod\PayMethodController;
use App\Http\Controllers\Api\V1\Products\ProductsController;
use App\Http\Controllers\Api\V1\Providers\ProvidersController;
use App\Http\Controllers\Api\V1\Roles\rolesController;
use App\Http\Controllers\Api\V1\Site\Company\CompanyOffersController;
use App\Http\Controllers\Api\V1\Site\Home\HomeController;
use App\Http\Controllers\Api\V1\Site\OfferOrder\OfferOrderController;
use App\Http\Controllers\Api\V1\Site\Pharmacy\SubUsersController;
use App\Http\Controllers\Api\V1\Site\Recipes\RecipesController;
use App\Http\Controllers\Api\V1\Site\Sales\SalesController;
use App\Http\Controllers\Api\V1\Site\Storehouse\StorehouseOffersController;
use App\Http\Controllers\Api\V1\Users\UsersController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::group(
    [],
    function () {
        // Roles
        Route::group(
            ['prefix' => 'roles'],
            function () {
                Route::get('signup_roles', [rolesController::class, 'getSignUpRoles']);
            }
        );

        // Pay Methods
        Route::group(
            ['prefix' => 'pay_methods'],
            function () {
                Route::get('', [PayMethodController::class, 'getAllPayMethods']);
            }
        );

        // Users For Select
        Route::group(['middleware' => 'auth:api'], function () {
            Route::group(
                ['prefix' => 'users'],
                function () {
                    Route::get('/storehouse', [UsersController::class, 'getUsersForSelectBox'])
                        ->middleware(['hasCompanyPermissions'])
                        ->name('roles-storehouse-all');
                    Route::get('/pharmacy', [UsersController::class, 'getUsersForSelectBox'])
                        ->middleware(['hasStorehousePermissions'])
                        ->name('roles-pharmacy-all');
                }
            );

            // Products

            Route::group(
                ['middleware' => ['auth:api', 'hasProductPermissions'], 'prefix' => 'products'],
                function () {
                    Route::get('doctor_products', [ProductsController::class, 'doctorProducts'])
                        ->withoutMiddleware('hasProductPermissions')
                        ->middleware('hasDoctorPermissions');
                    Route::get('', [ProductsController::class, 'index'])->name('v1-products-all');
                    Route::get('scientific_name', [ProductsController::class, 'ScientificNamesSelect']);
                    Route::get('commercial_name', [ProductsController::class, 'CommercialNamesSelect']);
                    Route::get('{product}', [ProductsController::class, 'show']);
                    Route::post('', [ProductsController::class, 'store'])->name('v1-products-store');
                },
            );

            // Sales
            Route::group(
                ['prefix' => 'sales', 'middleware' => ['hasSalesPermissions', 'auth:api']],
                function () {
                    Route::get('', [SalesController::class, 'index'])->name('pharmacy-sales-show');
                    Route::post('', [SalesController::class, 'store'])->name('pharmacy-sales-add');
                }
            );

            Route::apiResource('providers', ProvidersController::class);
        });

        // Markting Offers
        Route::get('markting_offers', [MarktingController::class, 'index']);
        Route::get('offer_duration', [CompanyOffersController::class, 'offerDurations']);
        // Providers
        Route::group(['prefix' => 'auth'], function () {
            // Login
            Route::post('/login', [AuthController::class, 'login'])->name('v1-login');
            // Signup
            Route::post('/signup', [AuthController::class, 'signup'])->name('v1-signup');

            // Logout
            Route::post('/logout', [AuthController::class, 'logout'])->name('v1-logout')
                ->middleware(['auth:api', 'isAuthenticated']);
        });

        Route::group(['middleware' => ['auth:api']], function () {
            // Public Site Routes
            Route::group(['prefix' => 'site'], function () {
                // Company

                Route::group(
                    ['prefix' => 'company', 'middleware' => ['hasCompanyPermissions']],
                    function () {
                        Route::get('', [HomeController::class, 'index']);
                        // Products
                        Route::group(
                            ['prefix' => 'products'],
                            function () {
                                Route::get('', [ProductsController::class, 'index']);
                                Route::get('/{product}', [ProductsController::class, 'show']);
                                Route::post('', [ProductsController::class, 'store']);
                            }
                        );
                        // Sales
                        Route::group(
                            ['prefix' => 'sales'],
                            function () {
                                Route::get('', [SalesController::class, 'index'])->name('company-sales-all');
                                Route::post('', [SalesController::class, 'store'])->name('company-sales-add');
                            }
                        );

                        // Company Offers
                        Route::group(
                            ['prefix' => 'company_offers'],
                            function () {
                                Route::get('', [CompanyOffersController::class, 'index']);
                                Route::get('{offer}', [CompanyOffersController::class, 'show']);
                                Route::post('', [CompanyOffersController::class, 'store']);
                                Route::put('/{offer}', [CompanyOffersController::class, 'update']);
                                Route::delete('/{offer}', [CompanyOffersController::class, 'destroy']);
                            }
                        );
                    }
                );

                // Storehouse
                Route::group(['prefix' => 'storehouse', 'middleware' => ['hasStorehousePermissions']], function () {
                    Route::get('', [HomeController::class, 'index']);
                    Route::group(
                        ['prefix' => 'products'],
                        function () {
                            Route::get('', [ProductsController::class, 'index']);
                            Route::post('', [ProductsController::class, 'store']);
                        }
                    );

                    // Storehouse Offers
                    Route::group(
                        ['prefix' => 'offers'],
                        function () {
                            Route::get('', [StorehouseOffersController::class, 'index']);
                            Route::get('{offer}', [StorehouseOffersController::class, 'show']);
                            Route::post('', [StorehouseOffersController::class, 'store']);
                            Route::put('/{offer}', [StorehouseOffersController::class, 'update']);
                            Route::delete('/{offer}', [StorehouseOffersController::class, 'destroy']);
                        }
                    );

                    // Company Offers
                    Route::group(
                        ['prefix' => 'company_offers'],
                        function () {
                            Route::get('', [OfferOrderController::class, 'index'])->name('order-company-show');
                            Route::post('', [OfferOrderController::class, 'order'])->name('order-company-make');
                        }
                    );
                    // Sales
                    Route::group(
                        ['prefix' => 'sales'],
                        function () {
                            Route::get('', [SalesController::class, 'index'])->name('storehouse-sales-all');
                            Route::post('', [SalesController::class, 'store'])->name('storehouse-sales-add');
                        }
                    );
                });

                // Pharmacy

                Route::group(
                    ['prefix' => 'pharmacy', 'namespace' => 'Pharmacy'],
                    function () {
                        Route::get('', [HomeController::class, 'index']);
                        // Products
                        Route::group(
                            ['prefix' => 'products'],
                            function () {
                                Route::get('', [ProductsController::class, 'index']);
                                Route::post('', [ProductsController::class, 'store']);
                            }
                        );

                        // Storehouse offers
                        Route::group(
                            ['prefix' => 'storehouse_offers'],
                            function () {
                                Route::get('', [OfferOrderController::class, 'index'])->name('order-storehouse-show');
                                Route::post('', [OfferOrderController::class, 'order'])->name('order-storehouse-make');
                            }
                        );

                        // Sales
                        Route::group(
                            ['prefix' => 'sales'],
                            function () {
                                Route::get('', [SalesController::class, 'index'])->name('pharmacy-sales-show');
                                Route::post('', [SalesController::class, 'store'])->name('pharmacy-sales-add');
                            }
                        );

                        // Sub Users
                        Route::group(['prefix' => 'sub_user'], function () {
                            Route::get('', [SubUsersController::class, 'index']);
                            Route::get('{subuser}', [SubUsersController::class, 'show']);
                            Route::post('', [SubUsersController::class, 'store']);
                            Route::put('{subuser}', [SubUsersController::class, 'update']);
                            Route::delete('{subuser}', [SubUsersController::class, 'destroy']);
                        }
                        );
                    }
                );

                // Doctor
                Route::group(
                    ['prefix' => 'doctor', 'middleware' => 'hasDoctorPermissions'],
                    function () {
                        // Recipes
                        Route::group(
                            ['prefix' => 'recipe'],
                            function () {
                                Route::get('', [RecipesController::class, 'getAllRecipes']);
                                Route::get('visitor_recipe', [RecipesController::class, 'getProductsWithRandomNumber']);
                                Route::post('visitor_recipe', [RecipesController::class, 'addRecipe']);
                            }
                        );

                        // Visitor
                        Route::group(
                            ['prefix' => 'visitor'],
                            function () {
                                Route::post('', [UsersController::class, 'registerNewVisitor']);
                                Route::post('forgot_random_number', [UsersController::class, 'ForgotVisitorRandomNumber']);
                            }
                        );
                    }
                );

                // Visitor
                Route::group(
                    ['prefix' => 'visitor', 'middleware' => ['hasVisitorPermissions']],
                    function () {
                        Route::group(
                            ['prefix' => 'archive'],
                            function () {
                                Route::get('', [ArchiveController::class, 'index']);
                                Route::get('{archive}', [ArchiveController::class, 'show']);
                                Route::post('', function (Request $request) {
                                    return ArchiveController::moveFromRandomNumberProducts($request);
                                });
                            }
                        );
                        Route::group(
                            ['prefix' => 'recipe'],
                            function () {
                                Route::get('', [RecipesController::class, 'index']);
                            }
                        );
                    }
                );
            });

            Route::group(['prefix' => 'mobile'], function () {
            });

            Route::group(
                ['prefix' => 'dashboard'],
                function () {
                    // Data Entry

                    Route::group(
                        ['prefix' => 'data_entry', 'middleware' => ['hasDataEntryPermissions']],
                        function () {
                            Route::get('', [ProductsController::class, 'index']);
                            Route::get('/{dataEntry}', [ProductsController::class, 'show']);
                            Route::post('', [ProductsController::class, 'store']);
                            Route::put('/{dataEntry}', [ProductsController::class, 'update']);
                            Route::delete('/{dataEntry}', [ProductsController::class, 'destroy']);
                        }
                    );

                    // Monitor And Evaluation

                    Route::group(
                        ['prefix' => 'monitor_and_evaluation', 'middleware' => ['hasMonitorAndEvaluationPermissions']],
                        function () {
                            Route::get('view', [MonitorAndEvaluationController::class, 'lang_content', 'monitorAndEvaluationController@lang_content']);
                            Route::get('', [MonitorAndEvaluationController::class, 'index']);
                            Route::get('/{user}', [MonitorAndEvaluationController::class, 'show']);
                            Route::post('', [MonitorAndEvaluationController::class, 'store']);
                            Route::put('/{user}', [MonitorAndEvaluationController::class, 'update']);
                            Route::delete('/{user}', [MonitorAndEvaluationController::class, 'destroy']);
                        }
                    );

                    // Human Resources

                    Route::group(
                        ['prefix' => 'human_resources', 'middleware' => ['hasHumanResourcePermissions']],
                        function () {
                            Route::get('', [HumanResourceController::class, 'index'])->name('human_resource_all');
                            Route::get('{user}', [HumanResourceController::class, 'show'])->name('human_resource_one');
                            Route::match(['POST', 'PUT'], '', [HumanResourceController::class, 'storeOrUpdate'])->name('human_resource_store');
                        }
                    );

                    // Markting

                    Route::group(
                        ['prefix' => 'markting', 'middleware' => ['hasMarktingPermissions']],
                        function () {
                            Route::get('', [MarktingController::class, 'index']);
                            Route::get('{ad}', [MarktingController::class, 'show']);
                            Route::post('', [MarktingController::class, 'store'])->name('markting_store');
                            Route::post('{ad}', [MarktingController::class, 'update'])->name('markting_update');
                            Route::delete('{ad}', [MarktingController::class, 'destroy']);
                        }
                    );

                    Route::group(
                        ['prefix' => 'order_management', 'middleware' => ['hasOrderManagementPermissions']],
                        function () {
                            Route::get('', [OrderManagementController::class, 'index']);
                            Route::post('{order}', [OrderManagementController::class, 'acceptPendingOrders']);
                        }
                    );
                }
            );
        });
    }
);
Route::get('/', function () {
    return 'This directory is working';
});
