<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="EquipmentResource",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Sword of Elendil"),
 *     @OA\Property(property="type", type="string", example="Sword"),
 *     @OA\Property(property="made_by", type="string", example="Elven Smiths"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2024-01-01T12:34:56Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2024-01-02T12:34:56Z")
 * )
 */

class EquipmentResource extends JsonResource
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
            'type' => $this->type,
            'made_by' => $this->made_by,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
