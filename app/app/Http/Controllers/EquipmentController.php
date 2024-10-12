<?php

namespace App\Http\Controllers;

use App\Exceptions\ApiException;
use App\Http\Requests\EquipmentRequest;
use App\Http\Resources\EquipmentResource;
use App\Models\Equipment;

class EquipmentController extends Controller
{
    public function index() {
        try {
            $this->authorize('viewAny', Equipment::class);
            $equipments = Equipment::paginate(10);
            return response()->json(EquipmentResource::collection($equipments), 200);
        } catch (\Throwable $th) {
            throw new ApiException('Unable to retrieve equipments', 400);
        }
    }

    public function store(EquipmentRequest $request) {
        try {
            $this->authorize('create', Equipment::class);
            $equipment = Equipment::create($request->validated());
            return response()->json(new EquipmentResource($equipment), 201);
        } catch (\Throwable $th) {
            throw new ApiException('Unable to create equipment', 400);
        }
    }

    public function update(EquipmentRequest $request, $id) {
        try {
            $equipment = Equipment::findOrFail($id);
            $this->authorize('update', $equipment);
            $equipment->update($request->validated());
            return response()->json(new EquipmentResource($equipment), 200);
        } catch (\Throwable $th) {
            throw new ApiException('Unable to update equipment', 400);
        }
    }

    public function destroy($id) {
        try {
            $equipment = Equipment::findOrFail($id);
            $this->authorize('delete', $equipment);
            $equipment->delete();
            return response()->json(['message' => 'Equipment deleted successfully'], 204);
        } catch (\Throwable $th) {
            throw new ApiException('Unable to delete equipment', 400);
        }
    }
}
