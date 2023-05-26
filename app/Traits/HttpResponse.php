<?php

namespace App\Traits;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Symfony\Component\HttpFoundation\Response;

trait HttpResponse
{
    /**
     * Error Response
     */
    public function error($data = null, int $code = Response::HTTP_NOT_FOUND, string $msg = 'Error Occurred'): JsonResponse
    {
        return response()->json([
            'data' => $data,
            'msg' => $msg,
            'type' => 'error',
            'code' => $code,
        ], $code);
    }

    /**
     * Success Response.
     */
    public function success(mixed $data = null, string $msg = 'Success', int $code = Response::HTTP_OK): JsonResponse
    {
        return response()->json([
            'data' => $data,
            'msg' => $msg,
            'type' => 'success',
            'code' => $code,
        ], $code);
    }

    /**
     * Response With Cookie.
     *
     * @param  mixed  $data
     * @param  mixed  $cookie
     */
    public function responseWithCookie($cookie, $data = null, string $msg = 'msg', string $type = 'success', int $code = 200): JsonResponse
    {
        return response()->json([
            'data' => $data,
            'msg' => $msg,
            'type' => $type,
            'code' => $code,
        ], $code)->withCookie($cookie);
    }

    /**
     * Validation Errors Response.
     *
     * @param  mixed  $data
     */
    public function validationErrorsResponse($data = null, int $code = Response::HTTP_UNPROCESSABLE_ENTITY, string $msg = 'validation errors'): JsonResponse
    {
        return response()->json([
            'data' => $data,
            'msg' => $msg,
            'type' => 'error',
            'code' => $code,
        ], $code);
    }

    public function unauthenticatedResponse(string $msg = 'You Are not authenticated', int $code = Response::HTTP_UNAUTHORIZED, $data = null): JsonResponse
    {
        return response()->json([
            'data' => $data,
            'msg' => $msg,
            'type' => 'error',
            'code' => $code,
        ], $code);
    }

    /**
     *  NotAuthenticated Response In Handler.
     *
     * @return never
     *
     * @throws AuthenticationException
     */
    public function throwNotAuthenticated()
    {
        throw new AuthenticationException();
    }

    /**
     * Undocumented function
     */
    public function resourceResponse($data, string $msg = 'Data Fetched Successfully', int $code = 200): JsonResponse
    {
        return response()->json([
            'data' => $data,
            'msg' => $msg,
            'code' => $code,
            'type' => 'success',
        ], $code);
    }

    /**
     * Return Forbidden Response
     *
     * @param  string  $msg
     * @param  null  $data
     */
    public function forbiddenResponse(string $msg = null, $data = null, int $code = Response::HTTP_FORBIDDEN): JsonResponse
    {
        $msg = $msg ?: __('messages.forbidden');

        return $this->error($data, $code, $msg);
    }

    public function noContentResponse(): \Illuminate\Http\Response
    {
        return response()->noContent();
    }

    public function notFoundResponse(string $msg = 'Not Found', array|null $data = null, int $code = Response::HTTP_NOT_FOUND): JsonResponse
    {
        return $this->error($data, $code, $msg);
    }

    public function createdResponse(array|null|JsonResource $data, string $msg = 'Resource Created Successfully', int $code = Response::HTTP_CREATED): JsonResponse
    {
        return response()->json([
            'data' => $data,
            'msg' => $msg,
            'code' => $code,
            'type' => 'success',
        ], $code);
    }
}
