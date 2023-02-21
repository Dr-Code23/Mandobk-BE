<?php

use App\Http\Controllers\Api\V1\Archive\ArchiveController;
use App\Http\Controllers\Api\V1\Auth\AuthController;
use App\Http\Controllers\Api\V1\Dashboard\HumanResourceController;
use App\Http\Controllers\Api\V1\Dashboard\MarktingController;
use App\Http\Controllers\Api\V1\Dashboard\MonitorAndEvaluationController;
use App\Http\Controllers\Api\V1\Dashboard\OrderManagementController;
use App\Http\Controllers\Api\V1\Mobile\Auth\MobileAuthController;
use App\Http\Controllers\Api\V1\PayMethod\PayMethodController;
use App\Http\Controllers\Api\V1\Products\ProductController;
use App\Http\Controllers\Api\V1\Profile\ProfileController;
use App\Http\Controllers\Api\V1\Roles\RoleController;
use App\Http\Controllers\Api\V1\Site\Home\HomeController;
use App\Http\Controllers\Api\V1\Site\OfferOrder\OfferOrderController;
use App\Http\Controllers\Api\V1\Site\Offers\OfferController;
use App\Http\Controllers\Api\V1\Site\Pharmacy\SubUserController;
use App\Http\Controllers\Api\V1\Site\Recipes\RecipeController;
use App\Http\Controllers\Api\V1\Site\Sales\SaleController;
use App\Http\Controllers\Api\V1\Users\UserController;
use App\Models\User;
use App\Models\V1\Role;
use App\Services\Api\V1\Products\ProductService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Roles
Route::group(
    ['prefix' => 'roles'],
    function () {
        Route::get('signup_roles', [RoleController::class, 'getSignUpRoles']);
        Route::get('monitor', [RoleController::class, 'monitorAndEvaluationRoles']);
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


    // Change Profile Info
    Route::post('profile', [ProfileController::class, 'changeProfileInfo']);
    // Markting Offers
    Route::get('markting_offers', [MarktingController::class, 'index']);

    // Pay Methods
    Route::group(
        ['prefix' => 'pay_methods'],
        function () {
            Route::get('', [PayMethodController::class, 'getAllPayMethods']);
        }
    );

    // Users For Select
    Route::group(
        ['prefix' => 'users', 'middleware' => ['auth:api']],
        function () {

            Route::get('/storehouse', [UserController::class, 'getUsersForSelectBox'])
                ->middleware(['hasCompanyPermissions'])
                ->name('roles-storehouse-all');

            Route::get('/human_resource', [UserController::class, 'getHumanResourceUsers'])
                ->middleware('hasHumanResourcePermissions');

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
            Route::get('scientific_name', [ProductController::class, 'ScientificNamesSelect'])->name('v1-products-scientific');
            Route::get('commercial_name', [ProductController::class, 'CommercialNamesSelect'])->name('v1-products-commercial');
            // ! Remove It After Testing
            Route::get('product_testing', [ProductService::class, 'testGetOneProduct'])->name('product_testing');
            Route::post('', [ProductController::class, 'storeOrUpdate'])->name('v1-products-store');
            Route::get('{product}', [ProductController::class, 'showWithoutDetails'])->name('v1-products-one');
        },
    );

    // Offers
    Route::group(['prefix' => 'offers', 'middleware' => ['App\Http\Middleware\hasOfferAccess']], function () {
        Route::get('', [OfferController::class, 'index'])->name('offer-all');
        Route::post('', [OfferController::class, 'store'])->name('offer-store');
        Route::get('{offer}', [OfferController::class, 'show'])->name('offer-one');
        Route::put('/{offer}', [OfferController::class, 'changeOfferStatus']);
        Route::delete('/{offer}', [OfferController::class, 'destroy']);
    });
    // Sales
    Route::group(
        ['prefix' => 'sales', 'middleware' => ['auth:api', 'hasSalesPermissions']],
        function () {
            Route::get('', [SaleController::class, 'index']);
            Route::post('', [SaleController::class, 'store']);
        }
    );

    // Public Site Routes
    Route::group(['prefix' => 'site'], function () {
        // Company

        Route::group(
            ['prefix' => 'company', 'middleware' => ['hasCompanyPermissions']],
            function () {
                Route::get('', [HomeController::class, 'index']);
            }
        );

        // Storehouse
        Route::group(['prefix' => 'storehouse', 'middleware' => ['hasStorehousePermissions']], function () {
            Route::get('', [HomeController::class, 'index']);

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
    });

    Route::group(['prefix' => 'mobile'], function () {
        Route::post('login', [MobileAuthController::class, 'login'])
            ->withoutMiddleware('auth:api');
        Route::post('logout', [MobileAuthController::class, 'logout']);

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
                        Route::get('', [RecipeController::class, 'getAllRecipes']);
                    }
                );
            }
        );
    });

    Route::group(
        ['prefix' => 'dashboard'],
        function () {

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
                    Route::post('', [MarktingController::class, 'store'])->name('markting_store');
                    Route::get('{ad}', [MarktingController::class, 'show']);
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


            // User Management

            Route::group(['prefix' => 'user_management'], function () {
                Route::get('', [UserController::class, 'getAllUsersInDashboardToApprove'])->name('dashboard-user-all');
                Route::put('{user}', [UserController::class, 'changeUserStatus'])->name('dashboard-user-update');
            });
        }
    );
});
Route::get('', function () {
    return Role::paginate(2);
});
Route::get('test', function () {
    auth()->login(User::find(1));
    return view('welcome');
});
