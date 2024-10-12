<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class EquipmentRequest extends FormRequest
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
            'name' => 'required|string|max:128',
            'type' => 'required|string|max:128',
            'made_by' => 'required|string|max:128',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'The team name is required.',
            'name.string' => 'The team name must be a string.',
            'name.max' => 'The team name cannot exceed 128 characters.',
            'type.required' => 'The equipment type is required.',
            'type.string' => 'The equipment type must be a string.',
            'type.max' => 'The equipment type cannot exceed 128 characters.',
            'made_by.required' => 'The equipment manufacturer is required.',
            'made_by.string' => 'The manufacturer must be a string.',
            'made_by.max' => 'The manufacturer cannot exceed 128 characters.',
        ];
    }
}