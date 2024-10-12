<?php

namespace App\Services;

use App\Exceptions\ApiException;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    public function authenticate($email, $password)
    {
        // Find the user by email
        $user = User::where('email', $email)->first();

        // Verify user and password
        if (!$user || !Hash::check($password, $user->password)) {
            throw new ApiException(
                'The provided credentials are incorrect.',
                401,
                [
                    'User email or password is incorrect'
                ]
            );
        }

        return $user;
    }
}
