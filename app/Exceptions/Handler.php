<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;


class Handler extends ExceptionHandler {
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register(){
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $exception){
        if ($request->expectsJson()) {
            if($exception instanceof ModelNotFoundException){
                return $this->model_not_found($exception);
            }
        }

        if ($request->isJson() && $exception instanceof ValidationException) {
            // return response()->json([
            //     'status' => 'error',
            //     'message' => [
            //         'errors' => $exception->getMessage(),
            //         'fields' => $exception->validator->getMessageBag()->toArray()
            //     ]
            // ], JsonResponse::HTTP_PRECONDITION_FAILED);
        }

        return parent::render($request, $exception);
    }

    public function model_not_found($exception){
        return response()->json([
            'status' => false,
            'message' => 'No Records Found For Id ' .implode(", ", $exception->getIds()),
            'errors' => $exception->getMessage()
        ], JsonResponse::HTTP_BAD_REQUEST);
    }
}
