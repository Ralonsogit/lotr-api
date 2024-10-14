<?php

namespace App\Http\Controllers\Api\V1;

use App\Exceptions\ApiException;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\UserRequest;
use Illuminate\Http\Request;
use App\Models\User;
use App\Services\AuthService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Throwable;
use OpenApi\Annotations as OA;

/**
 * @OA\OpenApi(
 *     @OA\Info(
 *         title="Auth API",
 *         version="1.0.0",
 *         description="Auth API description"
 *     )
 * )
 */

/**
 * @OA\Tag(
 *     name="Auth",
 *     description="API endpoints for user authentication"
 * )
 */
class AuthController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/v1/auth/register",
     *     tags={"Auth"},
     *     summary="Register a new user",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/UserRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="User registered successfully",
     *         @OA\JsonContent(ref="#/components/schemas/User")
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Unable to register user"
     *     )
     * )
     */
    public function register(UserRequest $request)
    {
        try {
            // Create a new user with hashed password
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            // Return the newly created user
            return response()->json(['user' => $user], 201);
        } catch (Throwable $e) {
            // Log error
            Log::error('ThrowableException when registering user:', ['user_id' => $request->user()->id]);
            throw new ApiException('Unable to register user', 400);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/v1/auth/login",
     *     tags={"Auth"},
     *     summary="Login a user and retrieve a token",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/LoginRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful login",
     *         @OA\JsonContent(
     *             @OA\Property(property="access_token", type="string", example="your_access_token_here"),
     *             @OA\Property(property="token_type", type="string", example="Bearer")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized - Invalid credentials"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Unable to log in"
     *     )
     * )
     */
    public function login(LoginRequest $request, AuthService $authService)
    {
        try {
            // Validate credentials & Cache store
            $user = Cache::remember("user_login_{$request->email}", 300, function () use ($authService, $request) {
                return $authService->authenticate($request->email, $request->password);
            });

            // Create a personal access token for the user
            $token = $user->createToken('auth_token')->plainTextToken;

            // Return the access token with token type
            return response()->json([
                'access_token' => $token,
                'token_type' => 'Bearer',
            ], 200);
        } catch (ApiException $e) {
            // Log error
            Log::error('ApiException when logging user:', ['user_id' => $request->user()->id]);
            throw $e;
        } catch (Throwable $th) {
            // Log error
            Log::error('ThrowableException when logging user:', ['user_id' => $request->user()->id]);
            throw new ApiException('Unable to log in', 400);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/v1/auth/logout",
     *     tags={"Auth"},
     *     summary="Logout a user and invalidate the token",
     *     @OA\Response(
     *         response=204,
     *         description="Successful logout"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized - User not logged in"
     *     )
     * )
     */
    public function logout(Request $request)
    {
        try {
            // Delete all user tokens to revoke authentication
            $request->user()->tokens()->delete();

            // Clean cache
            Cache::forget("user_login_{$request->user()->email}");

            return response()->noContent();
        } catch (Throwable $th) {
            // Log error
            Log::error('ThrowableException when logging out user:', ['user_id' => $request->user()->id]);
            throw new ApiException('Unable to log out', 400);
        }
    }
}
