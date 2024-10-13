<?php

namespace App\Http\Controllers\Api\V1;

use App\Exceptions\ApiException;
use App\Http\Requests\CharacterRequest;
use App\Http\Resources\CharacterResource;
use App\Models\Character;
use Illuminate\Support\Facades\Cache;
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
            $characters = Cache::remember('characters_index', 300, function () {
                return Character::with(['equipment', 'faction'])->paginate(10);
            });

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
     * Retrieve a specific character by its ID.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id) {
        try {
            // Fetch the character by ID or throw a 404 if not found
            $character = Cache::remember("character_{$id}", 300, function () use ($id) {
                return Character::with(['equipment', 'faction'])->findOrFail($id);
            });

            // Check if the user is authorized to view the character
            $this->authorize('view', $character);

            // Log the retrieval with the character ID
            Log::info('Fetched character', ['character_id' => $character->id]);

            // Return the character resource with a 200 status code
            return response()->json(new CharacterResource($character->load(['equipment', 'faction'])), 200);
        } catch (Throwable $th) {
            // Log the error if character retrieval fails
            Log::error('Failed to retrieve character', ['error' => $th->getMessage()]);

            // Throw a custom API exception with a 404 status code
            throw new ApiException('Character not found', 404);
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

            // Clear the characters index cache to ensure fresh data
            Cache::forget('characters_index');

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

            // Clear the specific character cache after update
            Cache::forget("character_{$id}");
            Cache::forget('characters_index');

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

            // Clear the characters index cache to ensure fresh data
            Cache::forget('characters_index');

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

    /**
     * Restore a soft-deleted character.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function restore($id) {
        try {
            // Fetch the soft-deleted character by ID or throw a 404 if not found
            $character = Character::withTrashed()->findOrFail($id);

            // Check if the user is authorized to restore the character
            $this->authorize('restore', $character);

            // Restore the character (remove it from soft-deleted state)
            $character->restore();

            // Clear the characters index cache to ensure fresh data
            Cache::forget('characters_index');

            // Log the restoration with the character ID
            Log::info('Character restored', ['character_id' => $character->id]);

            // Return the restored element with a 200 status code
            return response()->json(new CharacterResource($character->load(['equipment', 'faction'])), 200);
        } catch (Throwable $th) {
            // Log the error if character restoration fails
            Log::error('Failed to restore character', ['error' => $th->getMessage()]);

            // Throw a custom API exception with a 400 status code
            throw new ApiException('Unable to restore character', 400);
        }
    }

    /**
     * Permanently delete a soft-deleted character from the database (force delete).
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function forceDelete($id) {
        try {
            // Fetch the soft-deleted character by ID or throw a 404 if not found
            $character = Character::withTrashed()->findOrFail($id);

            // Check if the user is authorized to permanently delete the character
            $this->authorize('forceDelete', $character);

            // Permanently delete the character (remove it from the database entirely)
            $character->forceDelete();

            // Clear the characters index cache to ensure fresh data
            Cache::forget('characters_index');

            // Log the permanent deletion with the character ID
            Log::info('Character permanently deleted', ['character_id' => $character->id]);

            // Return a 204 status with no content (successful deletion)
            return response()->noContent();
        } catch (Throwable $th) {
            // Log the error if permanent deletion fails
            Log::error('Failed to permanently delete character', ['error' => $th->getMessage()]);

            // Throw a custom API exception with a 400 status code
            throw new ApiException('Unable to permanently delete character', 400);
        }
    }
}
