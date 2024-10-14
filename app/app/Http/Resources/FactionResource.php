<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="FactionResource",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="faction_name", type="string", example="The Fellowship of the Ring"),
 *     @OA\Property(property="description", type="string", example="A group formed to destroy the One Ring."),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2024-01-01T12:34:56Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2024-01-02T12:34:56Z")
 * )
 */

class FactionResource extends JsonResource
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
            'faction_name' => $this->faction_name,
            'description' => $this->description,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
