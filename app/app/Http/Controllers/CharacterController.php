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
    /**
     * Retrieve a paginated list of characters along with their equipment and faction relationships.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index() {
        try {
            // Check if the user is authorized to view any character
            $this->authorize('viewAny', Character::class);

            // Fetch characters with their related equipment and faction, and paginate results
            $characters = Character::with(['equipment', 'faction'])->paginate(10);

            // Log success with the total number of characters fetched
            Log::info('Fetched characters', ['character_count' => $characters->total()]);

            // Return a paginated collection of CharacterResource as JSON
            return response()->json(CharacterResource::collection($characters), 200);
        } catch (Throwable $th) {
            // Log the error if fetching characters fails
            Log::error('Failed to retrieve characters', ['error' => $th->getMessage()]);

            // Throw a custom API exception with a 400 status code
            throw new ApiException('Unable to retrieve characters', 400);
        }
    }

    /**
     * Store a newly created character in the database.
     *
     * @param CharacterRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CharacterRequest $request) {
        try {
            // Check if the user is authorized to create a new character
            $this->authorize('create', Character::class);

            // Create a new character with the validated request data
            $character = Character::create($request->validated());

            // Log the creation with the character ID
            Log::info('Character created', ['character_id' => $character->id]);

            // Return the created character resource, including related equipment and faction, with a 201 status code
            return response()->json(new CharacterResource($character->load(['equipment', 'faction'])), 201);
        } catch (Throwable $th) {
            // Log the error if character creation fails
            Log::error('Failed to create character', ['error' => $th->getMessage()]);

            // Throw a custom API exception with a 400 status code
            throw new ApiException('Unable to create character', 400);
        }
    }

    /**
     * Update the specified character in the database.
     *
     * @param CharacterRequest $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(CharacterRequest $request, $id) {
        try {
            // Fetch the character by ID or throw a 404 if not found
            $character = Character::findOrFail($id);

            // Check if the user is authorized to update the character
            $this->authorize('update', $character);

            // Update the character with validated request data
            $character->update($request->validated());

            // Log the update with the character ID
            Log::info('Character updated', ['character_id' => $character->id]);

            // Return the updated character resource, including related equipment and faction, with a 200 status code
            return response()->json(new CharacterResource($character->load(['equipment', 'faction'])), 200);
        } catch (Throwable $th) {
            // Log the error if updating the character fails
            Log::error('Failed to update character', ['error' => $th->getMessage()]);

            // Throw a custom API exception with a 400 status code
            throw new ApiException('Unable to update character', 400);
        }
    }

    /**
     * Soft delete the specified character in the database.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id) {
        try {
            // Fetch the character by ID or throw a 404 if not found
            $character = Character::findOrFail($id);

            // Check if the user is authorized to delete the character
            $this->authorize('delete', $character);

            // Store the character ID for logging before deletion
            $characterId = $character->id;

            // Soft delete the character (it won't be permanently removed)
            $character->delete();

            // Log the deletion with the character ID
            Log::info('Character deleted', ['character_id' => $characterId]);

            // Return a 204 status with no content (successful deletion)
            return response()->noContent();
        } catch (Throwable $th) {
            // Log the error if character deletion fails
            Log::error('Failed to delete character', ['error' => $th->getMessage()]);

            // Throw a custom API exception with a 400 status code
            throw new ApiException('Unable to delete character', 400);
        }
    }
}
