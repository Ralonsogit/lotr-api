<?php

namespace Tests\Unit;

use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class LoginRequestTest extends TestCase
{
    public function test_login_request_validation_passes_with_valid_data()
    {
        $request = new LoginRequest();

        $data = [
            'email' => 'test@example.com',
            'password' => 'password',
        ];

        $validator = Validator::make($data, $request->rules());

        $this->assertTrue($validator->passes());
    }

    public function test_login_request_validation_fails_with_invalid_data()
    {
        $request = new LoginRequest();

        $data = [
            'email' => 'invalid-email',
            'password' => '',
        ];

        $validator = Validator::make($data, $request->rules());

        $this->assertFalse($validator->passes());
        $this->assertArrayHasKey('email', $validator->errors()->messages());
        $this->assertArrayHasKey('password', $validator->errors()->messages());
    }
}
