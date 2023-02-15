<?php

use App\Http\Controllers\Api\V1\Archive\ArchiveController;
use App\Http\Controllers\Api\V1\Auth\AuthController;
use App\Http\Controllers\Api\V1\Dashboard\HumanResourceController;
use App\Http\Controllers\Api\V1\Dashboard\MarktingController;
use App\Http\Controllers\Api\V1\Dashboard\MonitorAndEvaluationController;
use App\Http\Controllers\Api\V1\Dashboard\OrderManagementController;
use App\Http\Controllers\Api\V1\PayMethod\PayMethodController;
use App\Http\Controllers\Api\V1\Products\ProductController;
use App\Http\Controllers\Api\V1\Providers\ProviderController;
use App\Http\Controllers\Api\V1\Roles\RoleController;
use App\Http\Controllers\Api\V1\Site\Company\CompanyOfferController;
use App\Http\Controllers\Api\V1\Site\Home\HomeController;
use App\Http\Controllers\Api\V1\Site\OfferOrder\OfferOrderController;
use App\Http\Controllers\Api\V1\Site\Pharmacy\SubUserController;
use App\Http\Controllers\Api\V1\Site\Recipes\RecipeController;
use App\Http\Controllers\Api\V1\Site\Sales\SaleController;
use App\Http\Controllers\Api\V1\Site\Storehouse\StorehouseOffersController;
use App\Http\Controllers\Api\V1\Users\UserController;
use App\Models\V1\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Roles
Route::group(
    ['prefix' => 'roles'],
    function () {
        Route::get('signup_roles', [RoleController::class, 'getSignUpRoles']);
    }
);

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
    // Markting Offers
    Route::get('markting_offers', [MarktingController::class, 'index']);

    // Offer Durations
    Route::get('offer_duration', [CompanyOfferController::class, 'offerDurations']);

    // Pay Methods
    Route::group(
        ['prefix' => 'pay_methods'],
        function () {
            Route::get('', [PayMethodController::class, 'getAllPayMethods']);
        }
    );

    // Users For Select
    Route::group(
        ['prefix' => 'users'],
        function () {
            Route::get('/storehouse', [UserController::class, 'getUsersForSelectBox'])
                ->middleware(['hasCompanyPermissions'])
                ->name('roles-storehouse-all');
            Route::get('/pharmacy', [UserController::class, 'getUsersForSelectBox'])
                ->middleware(['hasStorehousePermissions'])
                ->name('roles-pharmacy-all');
        }
    );

    // Products

    Route::group(
        ['middleware' => ['auth:api', 'hasProductPermissions'], 'prefix' => 'products'],
        function () {
            Route::get('doctor_products', [ProductController::class, 'doctorProducts'])
                ->withoutMiddleware('hasProductPermissions')
                ->middleware('hasDoctorPermissions');
            Route::get('', [ProductController::class, 'index'])->name('v1-products-all');
            Route::get('scientific_name', [ProductController::class, 'ScientificNamesSelect']);
            Route::get('commercial_name', [ProductController::class, 'CommercialNamesSelect']);
            Route::get('{product}', [ProductController::class, 'show']);
            Route::post('', [ProductController::class, 'store'])->name('v1-products-store');
        },
    );

    // Sales
    Route::group(
        ['prefix' => 'sales', 'middleware' => ['auth:api', 'hasSalesPermissions']],
        function () {
            Route::get('', [SaleController::class, 'index']);
            Route::post('', [SaleController::class, 'store']);
        }
    );

    // Providers
    Route::apiResource('providers', ProviderController::class);

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
                        Route::get('', [ProductController::class, 'index']);
                        Route::get('/{product}', [ProductController::class, 'show']);
                        Route::post('', [ProductController::class, 'store']);
                    }
                );
                // Company Offers
                Route::group(
                    ['prefix' => 'company_offers'],
                    function () {
                        Route::get('', [CompanyOfferController::class, 'index']);
                        Route::get('{offer}', [CompanyOfferController::class, 'show']);
                        Route::post('', [CompanyOfferController::class, 'store']);
                        Route::put('/{offer}', [CompanyOfferController::class, 'update']);
                        Route::delete('/{offer}', [CompanyOfferController::class, 'destroy']);
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
                    Route::get('', [ProductController::class, 'index']);
                    Route::post('', [ProductController::class, 'store']);
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
                        Route::get('', [ProductController::class, 'index']);
                        Route::post('', [ProductController::class, 'store']);
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

                // Sub Users
                Route::group(
                    ['prefix' => 'sub_user'],
                    function () {
                        Route::get('', [SubUserController::class, 'index']);
                        Route::get('{subuser}', [SubUserController::class, 'show']);
                        Route::post('', [SubUserController::class, 'store']);
                        Route::put('{subuser}', [SubUserController::class, 'update']);
                        Route::delete('{subuser}', [SubUserController::class, 'destroy']);
                    }
                );
            }
        );

        // Doctor
        Route::group(
            ['prefix' => 'doctor', 'middleware' => 'hasDoctorPermissions'],
            function () {
                // Recipe
                Route::group(
                    ['prefix' => 'recipe'],
                    function () {
                        Route::get('', [RecipeController::class, 'getAllRecipes']);
                        Route::get('visitor_recipe', [RecipeController::class, 'getProductsWithRandomNumber']);
                        Route::post('visitor_recipe', [RecipeController::class, 'addRecipe']);
                    }
                );

                // Visitor
                Route::group(
                    ['prefix' => 'visitor'],
                    function () {
                        Route::post('', [UserController::class, 'registerNewVisitor']);
                        Route::post('forgot_random_number', [UserController::class, 'ForgotVisitorRandomNumber']);
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
                        Route::get('', [RecipeController::class, 'index']);
                    }
                );
            }
        );
    });

    Route::group(['prefix' => 'mobile'], function () {
        // Route::post('login' , )
    });

    Route::group(
        ['prefix' => 'dashboard'],
        function () {
            // Data Entry

            Route::group(
                ['prefix' => 'data_entry', 'middleware' => ['hasDataEntryPermissions']],
                function () {
                    Route::get('', [ProductController::class, 'index']);
                    Route::get('/{dataEntry}', [ProductController::class, 'show']);
                    Route::post('', [ProductController::class, 'store']);
                    Route::put('/{dataEntry}', [ProductController::class, 'update']);
                    Route::delete('/{dataEntry}', [ProductController::class, 'destroy']);
                }
            );

            // Monitor And Evaluation

            Route::group(
                ['prefix' => 'monitor_and_evaluation', 'middleware' => ['hasMonitorAndEvaluationPermissions']],
                function () {
                    Route::get('view', [MonitorAndEvaluationController::class, 'lang_content', 'MonitorAndEvaluationController@lang_content']);
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

            // Order Management
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
Route::get('', function () {
    return Role::paginate(2);
});
