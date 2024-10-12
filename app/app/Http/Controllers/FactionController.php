<?php

namespace App\Http\Controllers;

use App\Exceptions\ApiException;
use App\Http\Requests\FactionRequest;
use App\Http\Resources\FactionResource;
use App\Models\Faction;
use Illuminate\Support\Facades\Log;

class FactionController extends Controller
{
    public function index() {
        try {
            $this->authorize('viewAny', Faction::class);
            $factions = Faction::paginate(10);
            Log::info('Retrieved factions', ['faction_count' => $factions->total()]);
            return response()->json(FactionResource::collection($factions), 200);
        } catch (\Throwable $th) {
            Log::error('Failed to retrieve factions', ['error' => $th->getMessage()]);
            throw new ApiException('Unable to retrieve factions', 400);
        }
    }

    public function store(FactionRequest $request) {
        try {
            $this->authorize('create', Faction::class);
            $faction = Faction::create($request->validated());
            Log::info('Faction created', ['faction_id' => $faction->id]);
            return response()->json(new FactionResource($faction), 201);
        } catch (\Throwable $th) {
            Log::error('Failed to create faction', ['error' => $th->getMessage()]);
            throw new ApiException('Unable to create faction', 400);
        }
    }

    public function update(FactionRequest $request, $id) {
        try {
            $faction = Faction::findOrFail($id);
            $this->authorize('update', $faction);
            $faction->update($request->validated());
            Log::info('Faction updated', ['faction_id' => $faction->id]);
            return response()->json(new FactionResource($faction), 200);
        } catch (\Throwable $th) {
            Log::error('Failed to update faction', ['error' => $th->getMessage()]);
            throw new ApiException('Unable to update faction', 400);
        }
    }

    public function destroy($id) {
        try {
            $faction = Faction::findOrFail($id);
            $this->authorize('delete', $faction);
            $faction->delete();
            Log::info('Faction deleted', ['character_id' => $factionDeleted->id]);
            return response()->json(['message' => 'Faction deleted successfully'], 204);
        } catch (\Throwable $th) {
            Log::error('Failed to delete faction', ['error' => $th->getMessage()]);
            throw new ApiException('Unable to delete faction', 400);
        }
    }
}
