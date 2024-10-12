<?php

namespace App\Http\Controllers;

use App\Exceptions\ApiException;
use App\Http\Requests\CharacterRequest;
use App\Http\Resources\CharacterResource;
use App\Models\Character;
use Throwable;

class CharacterController extends Controller
{
    public function index() {
        try {
            $this->authorize('viewAny', Character::class);
            $characters = Character::with(['equipment', 'faction'])->paginate(10);
            return response()->json(CharacterResource::collection($characters), 200);
        } catch (Throwable $th) {
            throw new ApiException('Unable to retrieve characters', 400);
        }
    }

    public function store(CharacterRequest $request) {
        try {
            $this->authorize('create', Character::class);
            $character = Character::create($request->validated());
            return response()->json(new CharacterResource($character->load(['equipment', 'faction'])), 201);
        } catch (Throwable $th) {
            throw new ApiException('Unable to create character', 400);
        }
    }

    public function update(CharacterRequest $request, $id) {
        try {
            $character = Character::findOrFail($id);
            $this->authorize('update', $character);
            $character->update($request->validated());
            return response()->json(new CharacterResource($character->load(['equipment', 'faction'])), 200);
        } catch (Throwable $th) {
            throw new ApiException('Unable to update character', 400);
        }
    }

    public function destroy($id) {
        try {
            $character = Character::findOrFail($id);
            $this->authorize('delete', $character);
            $character->delete();
            return response()->json(new CharacterResource($character, ['message' => 'Character deleted successfully']), 204);
        } catch (Throwable $th) {
            throw new ApiException('Unable to delete character', 400);
        }
    }
}
