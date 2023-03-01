<?php

use App\Http\Controllers\Api\V1\Archive\ArchiveController;
use App\Http\Controllers\Api\V1\Auth\AuthController;
use App\Http\Controllers\Api\V1\Dashboard\HumanResourceController;
use App\Http\Controllers\Api\V1\Dashboard\MarketingController;
use App\Http\Controllers\Api\V1\Dashboard\MonitorAndEvaluationController;
use App\Http\Controllers\Api\V1\Dashboard\OrderManagementController;
use App\Http\Controllers\Api\V1\Mobile\Auth\MobileAuthController;
use App\Http\Controllers\Api\V1\Notifications\NotificationController;
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
use App\Models\V1\Role;
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
        ->middleware(['auth:api']);
});

Route::group(['middleware' => ['auth:api']], function () {

    // Change Profile Info
    Route::post('profile', [ProfileController::class, 'changeProfileInfo']);
    // Markting Offers
    Route::get('markting_offers', [MarketingController::class, 'index']);

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
                ->middleware(['userHasPermissions:company,yes'])
                ->name('roles-storehouse-all');

            Route::get('/human_resource', [UserController::class, 'getHumanResourceUsers'])
                ->middleware('userHasPermissions:human_resource,no')
                ->name('roles-human_resource-all');

            Route::get('/pharmacy', [UserController::class, 'getUsersForSelectBox'])
                ->middleware(['userHasPermissions:storehouse,yes'])
                ->name('roles-pharmacy-all');
        }
    );

    // Products

    Route::group(
        ['middleware' => ['auth:api', 'hasProductPermissions'], 'prefix' => 'products'],
        function () {

            Route::get('doctor_products', [ProductController::class, 'doctorProducts'])
                ->withoutMiddleware('hasProductPermissions')
                ->middleware('userHasPermissions:doctor,yes')
                ->name('v1-products-doctor');

            Route::get('', [ProductController::class, 'index'])->name('v1-products-all');
            Route::get('scientific_name', [ProductController::class, 'scientificNamesSelect'])
                    ->name('v1-products-scientific');
            Route::get('commercial_name', [ProductController::class, 'commercialNamesSelect'])
                    ->name('v1-products-commercial');
            Route::post('', [ProductController::class, 'storeOrUpdate'])
                    ->name('v1-products-storeSubUser');
            Route::get('{product}', [ProductController::class, 'showWithoutDetails'])
                    ->name('v1-products-one');
        },
    );

    // Offers
    Route::group(['prefix' => 'offers', 'middleware' => ['App\Http\Middleware\hasOfferAccess']], function () {
        Route::get('', [OfferController::class, 'index'])->name('offer-all');
        Route::post('', [OfferController::class, 'store'])->name('offer-storeSubUser');
        Route::put('{offer}', [OfferController::class, 'changeOfferStatus'])->name('offer-status');
        Route::get('{offer}', [OfferController::class, 'show'])->name('offer-one');
        Route::delete('{offer}', [OfferController::class, 'destroy'])->name('offer-delete');
    });

    // Sales
    Route::group(
        ['prefix' => 'sales', 'middleware' => ['auth:api', 'hasSalesPermissions']],
        function () {
            Route::get('', [SaleController::class, 'index'])->name('sales-all');
            Route::post('', [SaleController::class, 'store'])->name('sales-storeSubUser');
        }
    );

    // Notifications

    Route::group(['prefix' => 'notifications'], function () {
        Route::get('', [NotificationController::class, 'index']);
        Route::put('', [NotificationController::class, 'markAllAsRead']);
        Route::delete('', [NotificationController::class, 'destroyAll']);
        Route::patch('{notification}', [NotificationController::class, 'markAsRead']);
        Route::get('{notification}', [NotificationController::class, 'show']);
        Route::delete('{notification}', [NotificationController::class, 'destroy']);
    });

    // Public Site Routes
    Route::group(['prefix' => 'site'], function () {
        // Company

        Route::group(
            ['prefix' => 'company', 'middleware' => ['userHasPermissions:company,yes']],
            function () {
                Route::get('', [HomeController::class, 'index']);
            }
        );

        // Storehouse
        Route::group(['prefix' => 'storehouse', 'middleware' => ['userHasPermissions:storehouse,yes']], function () {
            Route::get('', [HomeController::class, 'index']);

            // Company Offers
            Route::group(
                ['prefix' => 'company_offers'],
                function () {
                    Route::get('', [OfferOrderController::class, 'showAllOffers'])->name('order-company-showOneSubUser');
                    Route::post('', [OfferOrderController::class, 'order'])->name('order-company-make');
                }
            );
        });

        // Pharmacy

        Route::group(
            ['prefix' => 'pharmacy', 'middleware' => 'userHasPermissions:pharmacy,yes'],
            function () {
                Route::get('', [HomeController::class, 'index']);

                // Storehouse offers
                Route::group(
                    ['prefix' => 'storehouse_offers'],
                    function () {
                        Route::get('', [OfferOrderController::class, 'showAllOffers'])->name('order-storehouse-showOneSubUser');
                        Route::post('', [OfferOrderController::class, 'order'])->name('order-storehouse-make');
                    }
                );

                // Sub Users
                Route::group(
                    ['prefix' => 'sub_user', 'middleware' => ['userHasNoPermission:pharmacy_sub_user']],
                    function () {
                        Route::get('', [SubUserController::class, 'showAllSubUsers']);
                        Route::post('', [SubUserController::class, 'storeSubUser']);
                        Route::get('{subUser}', [SubUserController::class, 'showOneSubUser']);
                        Route::put('{subUser}', [SubUserController::class, 'updateSubUser']);
                        Route::delete('{subUser}', [SubUserController::class, 'destroy']);
                    }
                );

                Route::get('pharmacy_visits', [RecipeController::class, 'GetAllPharmacyRecipes']);
                Route::group(['prefix' => 'recipes'], function () {
                    Route::get('', [RecipeController::class, 'getProductsAssociatedWithRandomNumberForPharmacy']);
                    Route::put('{recipe}', [RecipeController::class, 'acceptVisitorRecipeFromPharmacy']);
                });
            }
        );

        // Doctor
        Route::group(
            ['prefix' => 'doctor', 'middleware' => 'userHasPermissions:doctor,yes'],
            function () {
                // Recipe
                Route::group(
                    ['prefix' => 'recipe'],
                    function () {
                        Route::get('', [RecipeController::class, 'getAllRecipes'])->name('doctor-recipe-all');
                        Route::get('visitor_recipe', [RecipeController::class, 'getProductsWithRandomNumber'])->name('doctor-visitor-products');
                        Route::post('visitor_recipe', [RecipeController::class, 'addRecipe'])->name('doctor-recipe-add');
                    }
                );

                // Visitor
                Route::group(
                    ['prefix' => 'visitor'],
                    function () {
                        Route::post('', [UserController::class, 'registerNewVisitor'])->name('doctor-visitor-register');
                        Route::post('forgot_random_number', [UserController::class, 'forgotVisitorRandomNumber'])->name('doctor-visitor-forgot-random-number');
                        Route::post('add_random_number', [UserController::class, 'addRandomNumberForVisitor']);
                    }
                );
            }
        );
    });

    // Mobile
    Route::group(['prefix' => 'mobile'], function () {
        Route::post('login', [MobileAuthController::class, 'login'])
            ->withoutMiddleware('auth:api')->name('mobile-login');
        Route::post('logout', [MobileAuthController::class, 'logout']);

        // Visitor
        Route::group(
            ['prefix' => 'visitor', 'middleware' => ['userHasPermissions:visitor,yes']],
            function () {
                Route::group(
                    ['prefix' => 'archive'],
                    function () {
                        Route::get('', [ArchiveController::class, 'index'])->name('archive-all');
                        Route::get('{archive}', [ArchiveController::class, 'show'])->name('archive-one');
                        Route::post('', [ArchiveController::class, 'moveProductsToArchive'])->name('archive-move');
                    }
                );
                Route::group(
                    ['prefix' => 'recipe'],
                    function () {
                        Route::get('', [RecipeController::class, 'getAllRecipes'])->name('visitor-all-recipes');
                    }
                );
            }
        );
    });

    // Dashboard
    Route::group(
        ['prefix' => 'dashboard'],
        function () {

            // Monitor And Evaluation

            Route::group(
                ['prefix' => 'monitor_and_evaluation', 'middleware' => ['userHasPermissions:monitor_and_evaluation,no']],
                function () {
                    Route::get('', [MonitorAndEvaluationController::class, 'index'])->name('monitor-all');
                    Route::get('/{user}', [MonitorAndEvaluationController::class, 'show'])->name('monitor-one');
                    Route::post('', [MonitorAndEvaluationController::class, 'store'])->name('monitor-storeSubUser');
                    Route::put('/{user}', [MonitorAndEvaluationController::class, 'update'])->name('monitor-updateSubUser');
                    Route::delete('/{user}', [MonitorAndEvaluationController::class, 'destroy'])->name('monitor-delete');
                }
            );

            // Human Resources

            Route::group(
                ['prefix' => 'human_resources', 'middleware' => ['userHasPermissions:human_resource,no']],
                function () {
                    Route::get('', [HumanResourceController::class, 'index'])->name('human_resource_all');
                    Route::get('{humanResource}', [HumanResourceController::class, 'show'])->name('human_resource_one');
                    Route::match(['POST', 'PUT'], '', [HumanResourceController::class, 'storeOrUpdate'])->name('human_resource_store_update');
                }
            );

            // Marketing

            Route::group(
                ['prefix' => 'markting', 'middleware' => ['userHasPermissions:markting,no']],
                function () {
                    Route::get('', [MarketingController::class, 'index'])->name('markting-all');
                    Route::post('', [MarketingController::class, 'store'])->name('markting_store');
                    Route::get('{ad}', [MarketingController::class, 'show'])->name('markting-one');
                    Route::post('{ad}', [MarketingController::class, 'update'])->name('markting_update');
                    Route::delete('{ad}', [MarketingController::class, 'destroy']);
                }
            );

            // Order Management
            Route::group(
                ['prefix' => 'order_management', 'middleware' => ['userHasPermissions:order_management,no']],
                function () {
                    Route::get('', [OrderManagementController::class, 'index'])->name('management-all');
                    Route::post('{order}', [OrderManagementController::class, 'managePendingOrders'])
                        ->name('management-accept');
                }
            );


            // User Management

            Route::group(['prefix' => 'user_management', 'middleware' => ['userHasPermissions:ceo,no']], function () {
                Route::get('', [UserController::class, 'getAllUsersToManage'])
                    ->name('dashboard-user-all');
                Route::put('{user}', [UserController::class, 'changeUserStatus'])
                    ->name('dashboard-user-updateSubUser');
            });
        }
    );
});
Route::get('paginate', function () {
    return Role::paginate(2);
});


//Route::get('pusher_test', function () {
//    return view('main');
//});

