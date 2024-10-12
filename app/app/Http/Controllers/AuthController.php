<?php

namespace App\Http\Controllers;

use App\Exceptions\ApiException;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\UserRequest;
use Illuminate\Http\Request;
use App\Models\User;
use App\Services\AuthService;
use Illuminate\Support\Facades\Hash;
use Throwable;

class AuthController extends Controller
{
    // Register method
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
            throw new ApiException('Unable to register user', 400);
        }
    }

    // Login method
    public function login(LoginRequest $request, AuthService $authService)
    {
        try {
            // Validate credentials
            $user = $authService->authenticate($request->email, $request->password);

            // Create a personal access token for the user
            $token = $user->createToken('auth_token')->plainTextToken;

            // Return the access token with token type
            return response()->json([
                'access_token' => $token,
                'token_type' => 'Bearer',
            ]);
        } catch (ApiException $e) {
            throw $e;
        } catch (Throwable $th) {
            throw new ApiException('Unable to log in', 400);
        }
    }

    // Logout method
    public function logout(Request $request)
    {
        try {
            // Delete all user tokens to revoke authentication
            $request->user()->tokens()->delete();
            return response()->json(['message' => 'Successfully logged out']);
        } catch (Throwable $th) {
            throw new ApiException('Unable to log out', 400);
        }
    }

    // Method to get authenticated user
    public function user(Request $request)
    {
        try {
            // Return the authenticated user
            return response()->json($request->user());
        } catch (Throwable $th) {
            throw new ApiException('Unable to retrieve user data', 400);
        }
    }
}
