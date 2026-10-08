<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePlacementRequest;
use App\Http\Requests\UpdatePlacementRequest;
use App\Http\Resources\PlacementResource;
use App\Models\Placement;
use App\Services\PlacementService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PlacementController extends Controller
{
    public function __construct(
        private PlacementService $placementService
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Placement::class);

        $perPage = min(
            max((int) $request->query('per_page', 10), 1),
            100
        );

        $placements = $this->placementService->list($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Placements retrieved successfully.',
            'data' => PlacementResource::collection($placements),
        ]);
    }

    public function store(StorePlacementRequest $request): JsonResponse
    {
        $this->authorize('create', Placement::class);

        $placement = $this->placementService->create(
            $request->validated(),
            $request->user()->id
        );

        return response()->json([
            'success' => true,
            'message' => 'Placement created successfully.',
            'data' => new PlacementResource($placement),
        ], 201);
    }

    public function show(Placement $placement): JsonResponse
    {
        $this->authorize('view', $placement);

        $placement->load([
            'student',
            'department',
            'supervisor',
            'assignedBy',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Placement retrieved successfully.',
            'data' => new PlacementResource($placement),
        ]);
    }

    public function update(
        UpdatePlacementRequest $request,
        Placement $placement
    ): JsonResponse {
        $this->authorize('update', $placement);

        $placement = $this->placementService->update(
            $placement,
            $request->validated()
        );

        return response()->json([
            'success' => true,
            'message' => 'Placement updated successfully.',
            'data' => new PlacementResource($placement),
        ]);
    }

    public function destroy(Placement $placement): JsonResponse
    {
        $this->authorize('delete', $placement);

        $this->placementService->delete($placement);

        return response()->json([
            'success' => true,
            'message' => 'Placement deleted successfully.',
            'data' => [],
        ]);
    }
}