<?php

namespace App\Http\Controllers;

use App\Exceptions\ApiException;
use App\Http\Requests\EquipmentRequest;
use App\Http\Resources\EquipmentResource;
use App\Models\Equipment;
use Illuminate\Support\Facades\Log;
use Throwable;

class EquipmentController extends Controller
{
    /**
     * Retrieve a paginated list of equipment.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index() {
        try {
            // Check if the user is authorized to view any equipment
            $this->authorize('viewAny', Equipment::class);

            // Fetch equipment with pagination (10 per page)
            $equipments = Equipment::paginate(10);

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
     * Store a newly created equipment in the database.
     *
     * @param EquipmentRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(EquipmentRequest $request) {
        try {
            // Check if the user is authorized to create equipment
            $this->authorize('create', Equipment::class);

            // Create a new equipment entry using validated request data
            $equipment = Equipment::create($request->validated());

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
     * Update the specified equipment in the database.
     *
     * @param EquipmentRequest $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(EquipmentRequest $request, $id) {
        try {
            // Fetch the equipment by ID or throw a 404 if not found
            $equipment = Equipment::findOrFail($id);

            // Check if the user is authorized to update the equipment
            $this->authorize('update', $equipment);

            // Update the equipment with validated request data
            $equipment->update($request->validated());

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
     * Soft delete the specified equipment in the database.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
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
     * Restore a soft-deleted equipment.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function restore($id) {
        try {
            // Fetch the soft-deleted equipment by ID or throw a 404 if not found
            $equipment = Equipment::withTrashed()->findOrFail($id);

            // Check if the user is authorized to restore the equipment
            $this->authorize('restore', $equipment);

            // Restore the equipment (remove it from soft-deleted state)
            $equipment->restore();

            // Log the restoration with the equipment ID
            Log::info('Equipment restored', ['equipment_id' => $equipment->id]);

            // Return a success message with a 200 status code
            return response()->json(['message' => 'Equipment restored successfully'], 200);
        } catch (Throwable $th) {
            // Log the error if equipment restoration fails
            Log::error('Failed to restore equipment', ['error' => $th->getMessage()]);

            // Throw a custom API exception with a 400 status code
            throw new ApiException('Unable to restore equipment', 400);
        }
    }

    /**
     * Permanently delete a soft-deleted equipment from the database (force delete).
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function forceDelete($id) {
        try {
            // Fetch the soft-deleted equipment by ID or throw a 404 if not found
            $equipment = Equipment::withTrashed()->findOrFail($id);

            // Check if the user is authorized to permanently delete the equipment
            $this->authorize('forceDelete', $equipment);

            // Permanently delete the equipment (remove it from the database entirely)
            $equipment->forceDelete();

            // Log the permanent deletion with the equipment ID
            Log::info('Equipment permanently deleted', ['equipment_id' => $equipment->id]);

            // Return a success message with a 200 status code
            return response()->json(['message' => 'Equipment permanently deleted successfully'], 200);
        } catch (Throwable $th) {
            // Log the error if permanent deletion fails
            Log::error('Failed to permanently delete equipment', ['error' => $th->getMessage()]);

            // Throw a custom API exception with a 400 status code
            throw new ApiException('Unable to permanently delete equipment', 400);
        }
    }
}
