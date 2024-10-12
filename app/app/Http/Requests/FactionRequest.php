<?php

namespace App\Http\Requests;

class FactionRequest extends BaseRequest
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
            'faction_name' => 'required|string|max:128',
            'description' => 'required|string|max:65535',
        ];
    }

    public function messages(): array
    {
        return [
            'faction_name.required' => 'The faction name is required.',
            'faction_name.string' => 'The faction name must be a string.',
            'faction_name.max' => 'The faction name cannot exceed 128 characters.',
            'description.string' => 'The description must be a string.',
            'description.max' => 'The description cannot exceed 65535 characters.',
        ];
    }
}
