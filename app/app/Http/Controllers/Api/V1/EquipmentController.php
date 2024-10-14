<?php

namespace App\Http\Controllers\Api\V1;

use App\Exceptions\ApiException;
use App\Http\Requests\EquipmentRequest;
use App\Http\Resources\EquipmentResource;
use App\Models\Equipment;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Throwable;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(
 *     name="Equipments",
 *     description="API endpoints for managing equipments"
 * )
 */
class EquipmentController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/equipments",
     *     tags={"Equipments"},
     *     summary="Retrieve a paginated list of equipment",
     *     @OA\Response(
     *         response=200,
     *         description="A paginated list of equipment",
     *         @OA\JsonContent(ref="#/components/schemas/EquipmentResource")
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Unable to retrieve equipments"
     *     ),
     *     security={{"bearerAuth": {}}},
     * )
     */
    public function index() {
        try {
            // Check if the user is authorized to view any equipment
            $this->authorize('viewAny', Equipment::class);

            // Fetch equipment with pagination (10 per page)
            $equipments = Cache::remember('equipments_index', 300, function () {
                return Equipment::paginate(10);
            });

            // Log success with the total number of equipment fetched
            Log::info('Fetched equipments', ['equipment_count' => $equipments->total()]);

            // Return the paginated equipment collection as a JSON response using EquipmentResource
            return response()->json(EquipmentResource::collection($equipments), 200);
        } catch (Throwable $th) {
            // Log the error in case of failure
            Log::error('Failed to retrieve equipments', ['error' => $th->getMessage()]);

            // Throw a custom API exception with a 400 status code
            throw new ApiException('Unable to retrieve equipments', 400);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/v1/equipments/{id}",
     *     tags={"Equipments"},
     *     summary="Retrieve a specific equipment by its ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the equipment",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Equipment found",
     *         @OA\JsonContent(ref="#/components/schemas/EquipmentResource")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Equipment not found"
     *     ),
     *     security={{"bearerAuth": {}}},
     * )
     */
    public function show($id) {
        try {
            // Fetch the equipment by ID or throw a 404 if not found
            $equipment = Cache::remember("equipment_{$id}", 300, function () use ($id) {
                return Equipment::findOrFail($id);
            });

            // Check if the user is authorized to view the equipment
            $this->authorize('view', $equipment);

            // Log the retrieval with the equipment ID
            Log::info('Fetched equipment', ['equipment_id' => $equipment->id]);

            // Return the equipment resource with a 200 status code
            return response()->json(new EquipmentResource($equipment), 200);
        } catch (Throwable $th) {
            // Log the error if equipment retrieval fails
            Log::error('Failed to retrieve equipment', ['error' => $th->getMessage()]);

            // Throw a custom API exception with a 404 status code
            throw new ApiException('Equipment not found', 404);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/v1/equipments",
     *     tags={"Equipments"},
     *     summary="Store a newly created equipment in the database",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/EquipmentRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Equipment created",
     *         @OA\JsonContent(ref="#/components/schemas/EquipmentResource")
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Unable to create equipment"
     *     ),
     *     security={{"bearerAuth": {}}},
     * )
     */
    public function store(EquipmentRequest $request) {
        try {
            // Check if the user is authorized to create equipment
            $this->authorize('create', Equipment::class);

            // Create a new equipment entry using validated request data
            $equipment = Equipment::create($request->validated());

            // Clear the equipments index cache to ensure fresh data
            Cache::forget('equipments_index');

            // Log the creation with the new equipment ID
            Log::info('Equipment created', ['equipment_id' => $equipment->id]);

            // Return the created equipment resource with a 201 status code
            return response()->json(new EquipmentResource($equipment), 201);
        } catch (Throwable $th) {
            // Log the error if equipment creation fails
            Log::error('Failed to create equipment', ['error' => $th->getMessage()]);

            // Throw a custom API exception with a 400 status code
            throw new ApiException('Unable to create equipment', 400);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/v1/equipments/{id}",
     *     tags={"Equipments"},
     *     summary="Update the specified equipment in the database",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the equipment to update",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/EquipmentRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Equipment updated",
     *         @OA\JsonContent(ref="#/components/schemas/EquipmentResource")
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Unable to update equipment"
     *     ),
     *     security={{"bearerAuth": {}}},
     * )
     */
    public function update(EquipmentRequest $request, $id) {
        try {
            // Fetch the equipment by ID or throw a 404 if not found
            $equipment = Equipment::findOrFail($id);

            // Check if the user is authorized to update the equipment
            $this->authorize('update', $equipment);

            // Update the equipment with validated request data
            $equipment->update($request->validated());

            // Clear the specific equipment cache after update
            Cache::forget("equipment_{$id}");
            Cache::forget('equipments_index');

            // Log the update with the equipment ID
            Log::info('Equipment updated', ['equipment_id' => $equipment->id]);

            // Return the updated equipment resource with a 200 status code
            return response()->json(new EquipmentResource($equipment), 200);
        } catch (Throwable $th) {
            // Log the error if equipment update fails
            Log::error('Failed to update equipment', ['error' => $th->getMessage()]);

            // Throw a custom API exception with a 400 status code
            throw new ApiException('Unable to update equipment', 400);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/equipments/{id}",
     *     tags={"Equipments"},
     *     summary="Soft delete the specified equipment in the database",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the equipment to delete",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Equipment deleted"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Unable to delete equipment"
     *     ),
     *     security={{"bearerAuth": {}}},
     * )
     */
    public function destroy($id) {
        try {
            // Fetch the equipment by ID or throw a 404 if not found
            $equipment = Equipment::findOrFail($id);

            // Check if the user is authorized to delete the equipment
            $this->authorize('delete', $equipment);

            // Store the equipment ID for logging before deletion
            $equipmentId = $equipment->id;

            // Soft delete the equipment (it won't be permanently removed)
            $equipment->delete();

            // Clear the equipments index cache to ensure fresh data
            Cache::forget('equipments_index');

            // Log the deletion with the equipment ID
            Log::info('Equipment deleted', ['equipment_id' => $equipmentId]);

            // Return a 204 status with no content (successful deletion)
            return response()->noContent();
        } catch (Throwable $th) {
            // Log the error if equipment deletion fails
            Log::error('Failed to delete equipment', ['error' => $th->getMessage()]);

            // Throw a custom API exception with a 400 status code
            throw new ApiException('Unable to delete equipment', 400);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/v1/equipments/{id}/restore",
     *     tags={"Equipments"},
     *     summary="Restore a soft-deleted equipment",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the equipment to restore",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Equipment restored",
     *         @OA\JsonContent(ref="#/components/schemas/EquipmentResource")
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Unable to restore equipment"
     *     ),
     *     security={{"bearerAuth": {}}},
     * )
     */
    public function restore($id) {
        try {
            // Fetch the soft-deleted equipment by ID or throw a 404 if not found
            $equipment = Equipment::withTrashed()->findOrFail($id);

            // Check if the user is authorized to restore the equipment
            $this->authorize('restore', $equipment);

            // Restore the equipment (remove it from soft-deleted state)
            $equipment->restore();

            // Clear the equipments index cache to ensure fresh data
            Cache::forget('equipments_index');

            // Log the restoration with the equipment ID
            Log::info('Equipment restored', ['equipment_id' => $equipment->id]);

            // Return the restored element with a 200 status code
            return response()->json(new EquipmentResource($equipment), 200);
        } catch (Throwable $th) {
            // Log the error if equipment restoration fails
            Log::error('Failed to restore equipment', ['error' => $th->getMessage()]);

            // Throw a custom API exception with a 400 status code
            throw new ApiException('Unable to restore equipment', 400);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/equipments/{id}/force",
     *     tags={"Equipments"},
     *     summary="Permanently delete a soft-deleted equipment from the database",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the equipment to permanently delete",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Equipment permanently deleted"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Unable to permanently delete equipment"
     *     ),
     *     security={{"bearerAuth": {}}},
     * )
     */
    public function forceDelete($id) {
        try {
            // Fetch the soft-deleted equipment by ID or throw a 404 if not found
            $equipment = Equipment::withTrashed()->findOrFail($id);

            // Check if the user is authorized to permanently delete the equipment
            $this->authorize('forceDelete', $equipment);

            // Permanently delete the equipment (remove it from the database entirely)
            $equipment->forceDelete();

            // Clear the equipments index cache to ensure fresh data
            Cache::forget('equipments_index');

            // Log the permanent deletion with the equipment ID
            Log::info('Equipment permanently deleted', ['equipment_id' => $equipment->id]);

            // Return a 204 status with no content (successful deletion)
            return response()->noContent();
        } catch (Throwable $th) {
            // Log the error if permanent deletion fails
            Log::error('Failed to permanently delete equipment', ['error' => $th->getMessage()]);

            // Throw a custom API exception with a 400 status code
            throw new ApiException('Unable to permanently delete equipment', 400);
        }
    }
}
