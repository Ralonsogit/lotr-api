<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;

class ApiException extends Exception
{
    protected $errors;

    public function __construct(string $message, int $code = 400, array $errors = [])
    {
        parent::__construct($message, $code);
        $this->errors = $errors;
    }

    public function toResponse($request): JsonResponse
    {
        return response()->json([
            'status' => false,
            'message' => $this->getMessage(),
            'errors' => $this->errors,
        ], $this->code);
    }
}