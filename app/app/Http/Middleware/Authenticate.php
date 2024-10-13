<?php

namespace App\Http\Middleware;

use App\Exceptions\ApiException;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        // Check if the request expects a JSON response
        if ($request->expectsJson()) {
            // Instead of redirecting, throw a JSON error response
            throw new ApiException('Unauthenticated.', 401);
        }

        // Otherwise, return null to prevent redirect
        return null;
    }
}
