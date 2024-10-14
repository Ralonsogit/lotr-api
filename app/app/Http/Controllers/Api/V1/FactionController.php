<?php

namespace App\Http\Controllers\Api\V1;

use App\Exceptions\ApiException;
use App\Http\Requests\FactionRequest;
use App\Http\Resources\FactionResource;
use App\Models\Faction;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Throwable;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(
 *     name="Factions",
 *     description="API endpoints for managing factions"
 * )
 */
class FactionController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/factions",
     *     tags={"Factions"},
     *     summary="Retrieve a paginated list of factions",
     *     @OA\Response(
     *         response=200,
     *         description="Successful retrieval of factions",
     *         @OA\JsonContent(ref="#/components/schemas/FactionResource")
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Unable to retrieve factions"
     *     )
     * )
     */
    public function index() {
        try {
            // Check if the user is authorized to view any factions
            $this->authorize('viewAny', Faction::class);

            // Fetch factions with pagination (10 per page)
            $factions = Cache::remember('factions_index', 300, function () {
                return Faction::paginate(10);
            });

            // Log success with the total number of factions
            Log::info('Retrieved factions', ['faction_count' => $factions->total()]);

            // Return the paginated factions as a JSON response using FactionResource
            return response()->json(FactionResource::collection($factions), 200);
        } catch (Throwable $th) {
            // Log the error in case of failure
            Log::error('Failed to retrieve factions', ['error' => $th->getMessage()]);

            // Throw a custom API exception with a 400 status code
            throw new ApiException('Unable to retrieve factions', 400);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/v1/factions/{id}",
     *     tags={"Factions"},
     *     summary="Retrieve a specific faction by its ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the faction to retrieve",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful retrieval of faction",
     *         @OA\JsonContent(ref="#/components/schemas/FactionResource")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Faction not found"
     *     )
     * )
     */
    public function show($id) {
        try {
            // Fetch the faction by ID or throw a 404 if not found
            $faction = Cache::remember("faction_{$id}", 300, function () use ($id) {
                return Faction::findOrFail($id);
            });

            // Check if the user is authorized to view the faction
            $this->authorize('view', $faction);

            // Log the retrieval with the faction ID
            Log::info('Fetched faction', ['faction_id' => $faction->id]);

            // Return the faction resource with a 200 status code
            return response()->json(new FactionResource($faction), 200);
        } catch (Throwable $th) {
            // Log the error if faction retrieval fails
            Log::error('Failed to retrieve faction', ['error' => $th->getMessage()]);

            // Throw a custom API exception with a 404 status code
            throw new ApiException('Faction not found', 404);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/v1/factions",
     *     tags={"Factions"},
     *     summary="Store a newly created faction",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/FactionRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Faction created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/FactionResource")
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Unable to create faction"
     *     )
     * )
     */
    public function store(FactionRequest $request) {
        try {
            // Check if the user is authorized to create a faction
            $this->authorize('create', Faction::class);

            // Create a new faction using validated request data
            $faction = Faction::create($request->validated());

            // Clear the factions index cache to ensure fresh data
            Cache::forget('factions_index');

            // Log the creation with the new faction ID
            Log::info('Faction created', ['faction_id' => $faction->id]);

            // Return the created faction resource with a 201 status code
            return response()->json(new FactionResource($faction), 201);
        } catch (Throwable $th) {
            // Log the error if faction creation fails
            Log::error('Failed to create faction', ['error' => $th->getMessage()]);

            // Throw a custom API exception with a 400 status code
            throw new ApiException('Unable to create faction', 400);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/v1/factions/{id}",
     *     tags={"Factions"},
     *     summary="Update the specified faction",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the faction to update",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/FactionRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Faction updated successfully",
     *         @OA\JsonContent(ref="#/components/schemas/FactionResource")
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Unable to update faction"
     *     )
     * )
     */
    public function update(FactionRequest $request, $id) {
        try {
            // Fetch the faction by ID or throw a 404 if not found
            $faction = Faction::findOrFail($id);

            // Check if the user is authorized to update the faction
            $this->authorize('update', $faction);

            // Update the faction with validated request data
            $faction->update($request->validated());

            // Clear the factions index cache to ensure fresh data
            Cache::forget("faction_{$id}");
            Cache::forget('factions_index');

            // Log the update with the faction ID
            Log::info('Faction updated', ['faction_id' => $faction->id]);

            // Return the updated faction resource with a 200 status code
            return response()->json(new FactionResource($faction), 200);
        } catch (Throwable $th) {
            // Log the error if faction update fails
            Log::error('Failed to update faction', ['error' => $th->getMessage()]);

            // Throw a custom API exception with a 400 status code
            throw new ApiException('Unable to update faction', 400);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/factions/{id}",
     *     tags={"Factions"},
     *     summary="Soft delete the specified faction",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the faction to delete",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Faction deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Unable to delete faction"
     *     )
     * )
     */
    public function destroy($id) {
        try {
            // Fetch the faction by ID or throw a 404 if not found
            $faction = Faction::findOrFail($id);

            // Check if the user is authorized to delete the faction
            $this->authorize('delete', $faction);

            // Store the faction ID for logging before deletion
            $factionId = $faction->id;

            // Soft delete the faction (it won't be permanently removed)
            $faction->delete();

            // Clear the factions index cache to ensure fresh data
            Cache::forget('factions_index');

            // Log the deletion with the faction ID
            Log::info('Faction deleted', ['faction_id' => $factionId]);

            // Return a 204 status with no content (successful deletion)
            return response()->noContent();
        } catch (Throwable $th) {
            // Log the error if faction deletion fails
            Log::error('Failed to delete faction', ['error' => $th->getMessage()]);

            // Throw a custom API exception with a 400 status code
            throw new ApiException('Unable to delete faction', 400);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/v1/factions/{id}/restore",
     *     tags={"Factions"},
     *     summary="Restore a soft-deleted faction",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the faction to restore",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Faction restored successfully",
     *         @OA\JsonContent(ref="#/components/schemas/FactionResource")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Faction not found"
     *     )
     * )
     */
    public function restore($id) {
        try {
            // Fetch the soft-deleted faction by ID or throw a 404 if not found
            $faction = Faction::withTrashed()->findOrFail($id);

            // Check if the user is authorized to restore the faction
            $this->authorize('restore', $faction);

            // Restore the faction (remove it from soft-deleted state)
            $faction->restore();

            // Clear the factions index cache to ensure fresh data
            Cache::forget('factions_index');

            // Log the restoration with the faction ID
            Log::info('Faction restored', ['faction_id' => $faction->id]);

            // Return the restored element with a 200 status code
            return response()->json(new FactionResource($faction), 200);
        } catch (Throwable $th) {
            // Log the error if faction restoration fails
            Log::error('Failed to restore faction', ['error' => $th->getMessage()]);

            // Throw a custom API exception with a 400 status code
            throw new ApiException('Unable to restore faction', 400);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/factions/{id}/force",
     *     tags={"Factions"},
     *     summary="Permanently delete a faction",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the faction to permanently delete",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Faction permanently deleted"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Faction not found"
     *     )
     * )
     */
    public function forceDelete($id) {
        try {
            // Fetch the soft-deleted faction by ID or throw a 404 if not found
            $faction = Faction::withTrashed()->findOrFail($id);

            // Check if the user is authorized to permanently delete the faction
            $this->authorize('forceDelete', $faction);

            // Permanently delete the faction (remove it from the database entirely)
            $faction->forceDelete();

            // Clear the factions index cache to ensure fresh data
            Cache::forget('factions_index');

            // Log the permanent deletion with the faction ID
            Log::info('Faction permanently deleted', ['faction_id' => $faction->id]);

            // Return a 204 status with no content (successful deletion)
            return response()->noContent();
        } catch (Throwable $th) {
            // Log the error if permanent deletion fails
            Log::error('Failed to permanently delete faction', ['error' => $th->getMessage()]);

            // Throw a custom API exception with a 400 status code
            throw new ApiException('Unable to permanently delete faction', 400);
        }
    }
}
