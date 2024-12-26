<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * default status code.
     *
     * @var int
     */
    protected $statusCode = 200;

    /**
     * get the status code.

     * @return int
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * set the status code.
     *
     * @param  int
     * @return $this
     */
    public function setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;

        return $this;
    }

    /**
     * Respond.
     *
     * @param  array $data
     * @param  array $headers
     * @return \Illuminate\Http\JsonResponse
     */
    public function respond($data, $headers = [])
    {
        return response()->json($data, $this->getStatusCode(), $headers);
    }

    /**
     * Respond Created.
     *
     * @param  array|string $data
     * @return \Illuminate\Http\JsonResponse
     */
    public function respondCreated($data)
    {
        return $this->setStatusCode(201)->respond([
            'data' => $data,
        ]);
    }

    /**
     * Respond Created with data.
     *
     * @param  array|string $data
     * @return \Illuminate\Http\JsonResponse
     */
    public function respondCreatedWithData($data)
    {
        return $this->setStatusCode(201)->respond($data);
    }

    /**
     * Respond with error
     *
     * @param  $message
     * @param  $code
     * @return \Illuminate\Http\JsonResponse
     */
    public function respondWithError($message, $code = 400)
    {
        return $this->setStatusCode($code)->respond([
            'error' => [
                'message'     => $message,
                'status_code' => $code,
            ]
        ]);
    }

    /**
     * Respond with error.
     *
     * @param  string $message
     * @return \Illuminate\Http\JsonResponse
     */
    public function respondInternalError($message = 'Internal Error')
    {
        return $this->setStatusCode(500)->respondWithError($message);
    }

    /**
     * Respond with unauthorized.
     *
     * @param  string $message
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondUnauthorized($message = 'Unauthorized')
    {
        return $this->setStatusCode(401)->respondWithError($message);
    }

    /**
     * Respond with forbidden.
     *
     * @param  string $message
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondForbidden($message = 'Forbidden')
    {
        return $this->setStatusCode(403)->respondWithError($message);
    }

    public function generateToken($strlnt = null) {
        $length = is_null($strlnt)? 20 : $strlnt;
        $hash = substr(md5(uniqid(mt_rand(),true)),0,$length);
        return $hash;
    }
}
