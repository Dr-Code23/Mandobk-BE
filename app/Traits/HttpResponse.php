<?php
namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

trait HttpResponse{
    /**
     * Error Response
     * @param array $data
     * @param int $code
     * @param string $message
     * @return JsonResponse
     */
    public function error($data = null, int $code = Response::HTTP_NOT_FOUND, string $msg = 'Error Occurred'): JsonResponse
    {
        return response()->json([
            'data' => $data,
            'msg' => $msg,
            'type' => 'error',
            'code' => $code,
        ]);
    }

    /**
     * Success Response
     * @param array $data
     * @param int $code
     * @param string $msg
     * @return JsonResponse
     */
    public function success($data = null  , string $msg = 'Success' , int $code = Response::HTTP_OK):JsonResponse{

        return response()->json([
            'data' => $data,
            'msg' => $msg,
            'type' => 'success',
            'code' => $code
        ] , $code);
    }

    /**
     * Response With Cookie
     * @param mixed $data
     * @param string $msg
     * @param string $type
     * @param int $code
     * @param mixed $cookie
     * @return JsonResponse
     */
    public function responseWithCookie($cookie,$data = null , string $msg = 'msg' , string $type ='success', int $code = 200 ):JsonResponse{
        return response()->json([
            'data' => $data,
            'msg' => $msg,
            'type' =>$type,
            'code' => $code
        ] , $code)->withCookie($cookie);
    }
    /**
     * Validation Errors Response
     * @param array $data
     * @param int $code
     * @param string $msg
     * @return JsonResponse
     */
    public function validation_errors($data = null , int $code = Response::HTTP_UNPROCESSABLE_ENTITY , string $msg = 'validation errors'):JsonResponse{

        return response()->json([
            'data' => $data,
            'msg' => $msg,
            'type' => 'error',
            'code' => $code
        ] , $code);
    }
}
