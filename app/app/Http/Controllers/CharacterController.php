<?php

namespace App\Http\Controllers;

use App\Exceptions\ApiException;
use App\Http\Requests\CharacterRequest;
use App\Http\Resources\CharacterResource;
use App\Models\Character;
use Illuminate\Support\Facades\Log;
use Throwable;

class CharacterController extends Controller
{
    public function index() {
        try {
            $this->authorize('viewAny', Character::class);
            $characters = Character::with(['equipment', 'faction'])->paginate(10);
            Log::info('Fetched characters', ['character_count' => $characters->total()]);
            return response()->json(CharacterResource::collection($characters), 200);
        } catch (Throwable $th) {
            Log::error('Failed to retrieve characters', ['error' => $th->getMessage()]);
            throw new ApiException('Unable to retrieve characters', 400);
        }
    }

    public function store(CharacterRequest $request) {
        try {
            $this->authorize('create', Character::class);
            $character = Character::create($request->validated());
            Log::info('Character created', ['character_id' => $character->id]);
            return response()->json(new CharacterResource($character->load(['equipment', 'faction'])), 201);
        } catch (Throwable $th) {
            Log::error('Failed to create character', ['error' => $th->getMessage()]);
            throw new ApiException('Unable to create character', 400);
        }
    }

    public function update(CharacterRequest $request, $id) {
        try {
            $character = Character::findOrFail($id);
            $this->authorize('update', $character);
            $character->update($request->validated());
            Log::info('Character updated', ['character_id' => $character->id]);
            return response()->json(new CharacterResource($character->load(['equipment', 'faction'])), 200);
        } catch (Throwable $th) {
            Log::error('Failed to update character', ['error' => $th->getMessage()]);
            throw new ApiException('Unable to update character', 400);
        }
    }

    public function destroy($id) {
        try {
            $character = Character::findOrFail($id);
            $this->authorize('delete', $character);
            $characterId = $character->id;
            $character->delete();
            Log::info('Character deleted', ['character_id' => $characterId]);
            return response()->json(new CharacterResource($character, ['message' => 'Character deleted successfully']), 204);
        } catch (Throwable $th) {
            Log::error('Failed to delete character', ['error' => $th->getMessage()]);
            throw new ApiException('Unable to delete character', 400);
        }
    }
}
