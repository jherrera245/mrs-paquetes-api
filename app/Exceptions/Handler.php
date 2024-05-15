<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Auth\Access\AuthorizationException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
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
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $exception)
    {
        if ($request->wantsJson() || $request->is('api/*')) {
            if ($exception instanceof NotFoundHttpException) {
                return response()->json(['message' => 'The requested link does not exist'], Response::HTTP_NOT_FOUND);
            }
    
            if ($exception instanceof ModelNotFoundException) {
                return response()->json(['message' => 'Item Not Found'], Response::HTTP_NOT_FOUND);
            }
    
            if ($exception instanceof AuthenticationException) {
                return response()->json(['message' => 'Unauthenticated'], Response::HTTP_UNAUTHORIZED);
            }
    
            if ($exception instanceof MethodNotAllowedHttpException) {
                return response()->json(['message' => 'This Method is not allowed for the requested route'], Response::HTTP_METHOD_NOT_ALLOWED);
            }
    
            if ($exception instanceof ValidationException) {
                return response()->json(['message' => 'Unprocessable Entity', 'errors' => $exception->errors()], Response::HTTP_UNPROCESSABLE_ENTITY);
            }
    
            if ($exception instanceof AuthorizationException) {
                return response()->json(['message' => 'You are not authorized to perform this action'], Response::HTTP_FORBIDDEN);
            }
    
            if ($exception instanceof QueryException) {
                return response()->json(['message' => 'Database Error', 'error' => $exception->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
    
            //Manejo del error 500 por defecto
            return response()->json(
                [
                    'message' => 'Internal Server Error',
                    'error' => $exception->getMessage()
                ], 
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        return parent::render($request, $exception);
    }

}
