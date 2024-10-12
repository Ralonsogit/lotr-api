<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;

class NotAdminException extends Exception
{
    protected $message = 'You must be an admin user';
    protected $code = 403;

    public function toResponse($request): JsonResponse
    {
        return response()->json([
            'message' => $this->message
        ], $this->code);
    }
}
