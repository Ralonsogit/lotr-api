<?php

namespace App\Http\Controllers;

use App\Exceptions\ApiException;
use App\Http\Requests\FactionRequest;
use App\Http\Resources\FactionResource;
use App\Models\Faction;
use Illuminate\Support\Facades\Log;
use Throwable;

class FactionController extends Controller
{
    /**
     * Retrieve a paginated list of factions.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index() {
        try {
            // Check if the user is authorized to view any factions
            $this->authorize('viewAny', Faction::class);

            // Fetch factions with pagination (10 per page)
            $factions = Faction::paginate(10);

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
     * Store a newly created faction in the database.
     *
     * @param FactionRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(FactionRequest $request) {
        try {
            // Check if the user is authorized to create a faction
            $this->authorize('create', Faction::class);

            // Create a new faction using validated request data
            $faction = Faction::create($request->validated());

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
     * Update the specified faction in the database.
     *
     * @param FactionRequest $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(FactionRequest $request, $id) {
        try {
            // Fetch the faction by ID or throw a 404 if not found
            $faction = Faction::findOrFail($id);

            // Check if the user is authorized to update the faction
            $this->authorize('update', $faction);

            // Update the faction with validated request data
            $faction->update($request->validated());

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
     * Soft delete the specified faction in the database.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
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
}
