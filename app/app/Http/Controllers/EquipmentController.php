<?php

namespace App\Http\Controllers;

use App\Exceptions\ApiException;
use App\Http\Requests\EquipmentRequest;
use App\Http\Resources\EquipmentResource;
use App\Models\Equipment;
use Illuminate\Support\Facades\Log;

class EquipmentController extends Controller
{
    public function index() {
        try {
            $this->authorize('viewAny', Equipment::class);
            $equipments = Equipment::paginate(10);
            Log::info('Fetched equipments', ['equipment_count' => $equipments->total()]);
            return response()->json(EquipmentResource::collection($equipments), 200);
        } catch (\Throwable $th) {
            Log::error('Failed to retrieve equipments', ['error' => $th->getMessage()]);
            throw new ApiException('Unable to retrieve equipments', 400);
        }
    }

    public function store(EquipmentRequest $request) {
        try {
            $this->authorize('create', Equipment::class);
            $equipment = Equipment::create($request->validated());
            Log::info('Equipment created', ['equipment_id' => $equipment->id]);
            return response()->json(new EquipmentResource($equipment), 201);
        } catch (\Throwable $th) {
            Log::error('Failed to create equipment', ['error' => $th->getMessage()]);
            throw new ApiException('Unable to create equipment', 400);
        }
    }

    public function update(EquipmentRequest $request, $id) {
        try {
            $equipment = Equipment::findOrFail($id);
            $this->authorize('update', $equipment);
            $equipment->update($request->validated());
            Log::info('Equipment updated', ['equipment_id' => $equipment->id]);
            return response()->json(new EquipmentResource($equipment), 200);
        } catch (\Throwable $th) {
            Log::error('Failed to update equipment', ['error' => $th->getMessage()]);
            throw new ApiException('Unable to update equipment', 400);
        }
    }

    public function destroy($id) {
        try {
            $equipment = Equipment::findOrFail($id);
            $this->authorize('delete', $equipment);
            $equipment->delete();
            Log::info('Equipment deleted', ['character_id' => $equipmentDeleted->id]);
            return response()->json(['message' => 'Equipment deleted successfully'], 204);
        } catch (\Throwable $th) {
            Log::error('Failed to delete equipment', ['error' => $th->getMessage()]);
            throw new ApiException('Unable to delete equipment', 400);
        }
    }
}
