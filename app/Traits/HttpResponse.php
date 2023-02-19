<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

trait HttpResponse
{
    /**
     * Error Response
     *
     * @param $data
     * @param int $code
     * @param string $msg
     * @return JsonResponse
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

    /**
     * Undocumented function
     *
     * @param $data
     * @param string $msg
     * @param integer $code
     * @return JsonResponse
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

    public function forbiddenResponse($msg = 'You do not have permissions to access this resource', $data = null, $code = Response::HTTP_FORBIDDEN)
    {
        return $this->error($data, $code, $msg);
    }

    public function noContentResponse()
    {
        return response()->noContent();
    }

    public function notFoundResponse(string $msg = 'Not Found', array|null $data = null, int $code = Response::HTTP_NOT_FOUND)
    {
        return $this->error($data, $code, $msg);
    }

    public function createdResponse(array|null|\Illuminate\Http\Resources\Json\JsonResource $data, string $msg = 'Resource Created Successfully', int $code = Response::HTTP_CREATED)
    {
        return response()->json([
            'data' => $data,
            'msg' => $msg,
            'code' => $code,
            'type' => 'success',
        ], $code);
    }
}
