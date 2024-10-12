<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
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
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        // Handling ApiException globally
        $this->renderable(function (ApiException $e, $request) {
            return $e->toResponse($request);
        });

        // Handling NotAdminException globally
        $this->renderable(function (NotAdminException $e, $request) {
            return $e->toResponse($request);
        });
    }
}
