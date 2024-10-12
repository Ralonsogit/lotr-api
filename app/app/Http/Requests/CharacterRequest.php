<?php

namespace App\Http\Requests;

class CharacterRequest extends BaseRequest
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
            'birth_date' => 'required|date',
            'kingdom' => 'required|string|max:128',
            'equipment_id' => 'required|integer|exists:equipments,id',
            'faction_id' => 'required|integer|exists:factions,id',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'The character name is required.',
            'name.string' => 'The character name must be a string.',
            'name.max' => 'The character name cannot exceed 128 characters.',
            'birth_date.required' => 'The birth date is required.',
            'birth_date.date' => 'The birth date must be a valid date.',
            'kingdom.required' => 'The kingdom is required.',
            'kingdom.string' => 'The kingdom must be a string.',
            'kingdom.max' => 'The kingdom cannot exceed 128 characters.',
            'equipment_id.required' => 'The equipment ID is required.',
            'equipment_id.integer' => 'The equipment ID must be an integer.',
            'equipment_id.exists' => 'The selected equipment is invalid.',
            'faction_id.required' => 'The faction ID is required.',
            'faction_id.integer' => 'The faction ID must be an integer.',
            'faction_id.exists' => 'The selected faction is invalid.',
        ];
    }
}
