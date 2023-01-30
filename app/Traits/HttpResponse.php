<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Symfony\Component\HttpFoundation\Response;

trait HttpResponse
{
    /**
     * Error Response.
     *
     * @param mixed  $data
     * @param string $message
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
     *
     * @param mixed $data
     */
    public function success($data = null, string $msg = 'Success', int $code = Response::HTTP_OK): JsonResponse
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
     * @param mixed $data
     * @param mixed $cookie
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
     * @param mixed $data
     */
    public function validation_errors($data = null, int $code = Response::HTTP_UNPROCESSABLE_ENTITY, string $msg = 'validation errors'): JsonResponse
    {
        return response()->json([
            'data' => $data,
            'msg' => $msg,
            'type' => 'error',
            'code' => $code,
        ], $code);
    }

    public function unauthenticatedResponse(string $msg = 'You Are not authenticated', int $code = Response::HTTP_FORBIDDEN, $data = null)
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
     * @throws \Illuminate\Auth\AuthenticationException
     */
    public function throwNotAuthenticated()
    {
        throw new \Illuminate\Auth\AuthenticationException();
    }

    public function resourceResponse(array|JsonResource $data, string $msg = 'Data Fetched Successfully', int $code = 200)
    {
        return [
            'data' => $data,
            'msg' => $msg,
            'code' => $code,
            'type' => 'success',
        ];
    }

    public function forbiddenResponse($msg = 'You do not have permissions to access this resource', $data = null, $code = Response::HTTP_FORBIDDEN)
    {
        return $this->error($data, $code, $msg);
    }

    public function noContentResponse()
    {
        return response()->json(status: Response::HTTP_NO_CONTENT);
    }
}
