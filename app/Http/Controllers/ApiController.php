<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    protected $message;
    protected $statusCode;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Return generic json response with the given data.
     *
     * @param $data
     * @param int $statusCode
     * @param array $headers
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respond($data, $statusCode = 200, $headers = [])
    {
        return response()->json($data, $statusCode, $headers);
    }

    /**
     * @param $data
     * @param int $statusCode
     * @param string $message
     * @param array $headers
     *
     * @return array
     */
    protected function respondSuccess($data = [], $statusCode = 200, array $headers = [])
    {
        $response['status'] = true;
        !empty($this->message) ? $response['message'] = $this->message : null;
        !empty($data) ? $response['data'] = $data : null;

        return $this->respond($response, $statusCode, $headers);
    }

    /**
     * Respond with created.
     *
     * @param $data
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondCreated($data)
    {
        $this->statusCode = $this->statusCode ?? 201;
        $response['status'] = true;
        !empty($data) ? $response['data'] = $data : null;
        !empty($this->message) ? $response['message'] = $this->message : null;

        return $this->respond($response, $this->statusCode);
    }
 
    /**
     * Respond with error.
     *
     * @param $message
     * @param $statusCode
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondError($message, $statusCode = 400)
    {
        return $this->respond([
            'status' => false,
            'errors' => [
                'message' => $message,
                'status_code' => $statusCode
            ]
        ], $statusCode);
    }

    /**
     * Respond with no content.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondNoContent()
    {
        return $this->respond(null, 204);
    }

    /**
     * Respond with forbidden.
     *
     * @param string $message
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondForbidden($message = 'Forbidden')
    {
        return $this->respondError($message, 403);
    }

    /**
     * Respond with not found.
     *
     * @param string $message
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondNotFound($message = 'Not Found')
    {
        return $this->respondError($message, 404);
    }

    /**
     * Respond with internal error.
     *
     * @param string $message
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondInternalError($message = 'Internal Error')
    {
        return $this->respondError($message, 500);
    }

    /**
     * Respond with pagination.
     *
     * @param $paginated
     * @param int $statusCode
     * @param array $headers
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithPagination($object, $statusCode = 200, $headers = [])
    {
        /*
            if $paginated is 10 then from should be 0 Or 11 Or 21
            if $paginated is 25 then from should be 0 Or 26 Or 51

            Request would be
            {
                "paginate":10,
                "from": 11,
                "columns": ["id", "title"]
            }
        */

        $columns = $this->request->get('columns', ['*']);
        $paginated = $this->request->get('paginate', 10);
        $from = $this->request->get('from', 0);
        $from_paginate = $from / $paginated + 0.90;

        $data = $object->paginate($paginated, $columns, 'page', $from_paginate)->toArray();

        $response['status'] = true;
        !empty($data) ? $response['data'] = $data['data'] : null;
        !empty($data) ? $response['total'] = $data['total'] : null;
        !empty($data) ? $response['from'] = $data['from'] : 0;
        !empty($data) ? $response['to'] = $data['to'] : 0;
        !empty($this->message) ? $response['message'] = $this->message : null;

        return $this->respond($response, $statusCode, $headers);
    }
}