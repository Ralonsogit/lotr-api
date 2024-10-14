<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="CharacterResource",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Aragorn"),
 *     @OA\Property(property="birth_date", type="string", format="date", example="1980-01-15"),
 *     @OA\Property(property="kingdom", type="string", example="Gondor"),
 *     @OA\Property(property="equipment", ref="#/components/schemas/EquipmentResource"),
 *     @OA\Property(property="faction", ref="#/components/schemas/FactionResource"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2024-01-01T12:34:56Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2024-01-02T12:34:56Z")
 * )
 */

class CharacterResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'birth_date' => $this->birth_date,
            'kingdom' => $this->kingdom,
            'equipment' => new EquipmentResource($this->whenLoaded('equipment')),
            'faction' => new FactionResource($this->whenLoaded('faction')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
