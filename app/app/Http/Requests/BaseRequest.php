<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class BaseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            //
        ];
    }

    protected function failedValidation(Validator $validator) {
        $response = response()->json([
            'success' => false,
            'message' => 'Validation errors occurred.',
            'errors' => $validator->errors()
        ], 422);

        throw new ValidationException($validator, $response);
    }

    protected function prepareForValidation()
    {
        $input = $this->all();
        $allowedKeys = array_keys($this->rules());

        // Filter allowed keys to include those that are in rules and those ending with '_confirmation'
        $allowedKeys = array_merge($allowedKeys, array_filter(array_keys($input), function ($key) {
            return str_ends_with($key, '_confirmation');
        }));

        $extraFields = array_diff(array_keys($input), $allowedKeys);

        // If more than required fields, throw exception
        if (count($extraFields) > 0) {
            throw new ValidationException(
                validator($input, $this->rules()),
                response()->json([
                    'message' => 'Unexpected fields provided: ' . implode(', ', $extraFields),
                    'success' => false,
                ], 400)
            );
        }
    }
}
