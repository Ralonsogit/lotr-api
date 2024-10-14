<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

/**
     * @OA\Info(
     *      version="1.0.0",
     *      title="LOTR API",
     *      description="Description of LOTR API",
     *      @OA\Contact(
     *          email="admin@admin.com"
     *      )
     * )
     *
     * @OA\Server(
     *      url="url",
     *      description="Demo API Server"
     * )

     *
     * @OA\Tag(
     *     name="API",
     *     description="API"
     * )
     */
class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
}
