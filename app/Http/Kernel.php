<?php

namespace App\Http;

use App\Http\Middleware\UserHasPermissions;
use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array<int, class-string|string>
     */
    protected $middleware = [
        // \App\Http\Middleware\TrustHosts::class,
        \App\Http\Middleware\TrustProxies::class,
        \Illuminate\Http\Middleware\HandleCors::class,
        \App\Http\Middleware\PreventRequestsDuringMaintenance::class,
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
        \App\Http\Middleware\TrimStrings::class,
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array<string, array<int, class-string|string>>
     */
    protected $middlewareGroups = [
        'web' => [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],

        'api' => [
            // \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
            'throttle:api',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],
    ];

    /**
     * The application's route middleware.
     *
     * These middleware may be assigned to groups or used individually.
     *
     * @var array<string, class-string|string>
     */
    protected $routeMiddleware = [
        'auth' => \App\Http\Middleware\Authenticate::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'auth.session' => \Illuminate\Session\Middleware\AuthenticateSession::class,
        'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
        'can' => \Illuminate\Auth\Middleware\Authorize::class,
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'password.confirm' => \Illuminate\Auth\Middleware\RequirePassword::class,
        'signed' => \App\Http\Middleware\ValidateSignature::class,
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
        'isAuthenticated' => \App\Http\Middleware\IsAuthenticated::class,
        // 'hasDataEntryPermissions' => \App\Http\Middleware\HasDataEntryAccess::class,
        // 'hasMonitorAndEvaluationPermissions' => \App\Http\Middleware\HasMonitorAndEvaluationAccess::class,
        // 'hasHumanResourcePermissions' => \App\Http\Middleware\HasHumanResourceAccess::class,
        // 'hasMarktingPermissions' => \App\Http\Middleware\HasMarktingAccess::class,
        // 'hasCompanyPermissions' => \App\Http\Middleware\HasCompanyAccess::class,
        // 'hasStorehousePermissions' => \App\Http\Middleware\HasStorehouseAccess::class,
        // 'hasDoctorPermissions' => \App\Http\Middleware\HasDoctorAccess::class,
        // 'hasVisitorPermissions' => \App\Http\Middleware\HasVisitorAccess::class,
        // 'hasOrderManagementPermissions' => \App\Http\Middleware\HasOrderManagementAccess::class,
        'hasProductPermissions' => \App\Http\Middleware\HasProductAccess::class,
        'hasSalesPermissions' => \App\Http\Middleware\HasSalesAccess::class,
        'hasNotificationsAccess' => \App\Http\Middleware\hasNotificationAccess::class,
        'userHasPermissions' => UserHasPermissions::class,
    ];
}
