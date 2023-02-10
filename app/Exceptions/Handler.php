<?php

namespace App\Exceptions;

use App\Traits\HttpResponse;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Handler extends ExceptionHandler
{
    use HttpResponse;
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (\Throwable $e) {
        });

        // Handle Unauthorized User
        $this->renderable(function (\Illuminate\Auth\AuthenticationException $e, $req) {
            // Check if the route in api
            if ($req->is('v1/*')) {
                // Return anauthenticated user response from HttpResponse Trait
                return $this->unauthenticatedResponse('You are not authenticated');
            }
        });

        // Handle Not Found Response
        $this->renderable(function (NotFoundHttpException $e, $req) {
            if ($req->is('v1/*')) {
                $msg = $e->getMessage();
                return $this->error($msg, 404, 'Not Found');
            }
        });
        // Method not allowed
        $this->renderable(function (\Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException $e, $request) {
            if ($request->is('v1/*')) {
                return $this->error(null, 405, $e->getMessage());
            }
        });
    }
}
