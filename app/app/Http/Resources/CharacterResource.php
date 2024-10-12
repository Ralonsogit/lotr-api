<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

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
