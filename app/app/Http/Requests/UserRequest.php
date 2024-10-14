<?php

namespace App\Http\Requests;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="UserRequest",
 *     required={"name", "email", "password"},
 *     @OA\Property(property="name", type="string"),
 *     @OA\Property(property="email", type="string", format="email"),
 *     @OA\Property(property="password", type="string"),
 * )
 */
class UserRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:4|confirmed',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'The name field is required.',
            'email.required' => 'The email field is required.',
            'email.email' => 'The email field must be a valid email address.',
            'email.unique' => 'The email is already in use.',
            'password.required' => 'The password field is required.',
            'password.confirmed' => 'The passwords do not match.',
            'password.min' => 'The password must be at least 4 characters long.',
        ];
    }
}
