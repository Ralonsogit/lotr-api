<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Render an exception into an HTTP response.
     */
    public function render($request, Throwable $exception): JsonResponse
    {
        // Custom exception for API
        if ($request->expectsJson()) {
            // ApiException, NotAdminException
            if ($exception instanceof ApiException || $exception instanceof NotAdminException) {
                return $exception->toResponse($request);
            }

            // Any exception on JSON call
            $statusCode = 500;
            if ($exception instanceof HttpException) {
                $statusCode = $exception->getStatusCode();
            } else {
                $statusCode = $exception->getCode() ? ($exception->getCode() < 100 ? 500 : $exception->getCode()) : 500;
            }
            $apiException = new ApiException(
                $exception->getMessage() ?? 'Something went wrong',
                $statusCode,
            );
            return $apiException->toResponse($request);
        }

        // Laravel web exceptions
        return parent::render($request, $exception);
    }

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        //
    }
}
