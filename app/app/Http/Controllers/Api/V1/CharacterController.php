<?php

namespace App\Http\Controllers\Api\V1;

use App\Exceptions\ApiException;
use App\Http\Requests\CharacterRequest;
use App\Http\Resources\CharacterResource;
use App\Models\Character;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Throwable;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(
 *     name="Characters",
 *     description="Endpoints for managing characters."
 * )
 */
class CharacterController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/characters",
     *     tags={"Characters"},
     *     summary="Retrieve a paginated list of characters",
     *     @OA\Response(response="200", description="A paginated list of characters returned successfully.")
     * )
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
     * @OA\Get(
     *     path="/api/v1/characters/{id}",
     *     tags={"Characters"},
     *     summary="Retrieve a specific character by ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the character to retrieve",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response="200", description="Character retrieved successfully."),
     *     @OA\Response(response="404", description="Character not found.")
     * )
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
     * @OA\Post(
     *     path="/api/v1/characters",
     *     tags={"Characters"},
     *     summary="Store a newly created character",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/CharacterRequest")
     *     ),
     *     @OA\Response(response="201", description="Character created successfully."),
     *     @OA\Response(response="400", description="Unable to create character.")
     * )
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
     * @OA\Put(
     *     path="/api/v1/characters/{id}",
     *     tags={"Characters"},
     *     summary="Update a character",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the character to update",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/CharacterRequest")
     *     ),
     *     @OA\Response(response="200", description="Character updated successfully."),
     *     @OA\Response(response="404", description="Character not found."),
     *     @OA\Response(response="400", description="Unable to update character.")
     * )
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
     * @OA\Delete(
     *     path="/api/v1/characters/{id}",
     *     tags={"Characters"},
     *     summary="Soft delete a character",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the character to delete",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response="204", description="Character deleted successfully."),
     *     @OA\Response(response="404", description="Character not found."),
     *     @OA\Response(response="400", description="Unable to delete character.")
     * )
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
     * @OA\Post(
     *     path="/api/v1/characters/{id}/restore",
     *     tags={"Characters"},
     *     summary="Restore a soft-deleted character",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the character to restore",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response="200", description="Character restored successfully."),
     *     @OA\Response(response="404", description="Character not found."),
     *     @OA\Response(response="400", description="Unable to restore character.")
     * )
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
     * @OA\Delete(
     *     path="/api/v1/characters/{id}/force-delete",
     *     tags={"Characters"},
     *     summary="Permanently delete a character",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the character to permanently delete",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response="204", description="Character permanently deleted successfully."),
     *     @OA\Response(response="404", description="Character not found."),
     *     @OA\Response(response="400", description="Unable to permanently delete character.")
     * )
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
