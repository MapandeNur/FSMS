<?php

namespace App\Http\Controllers;

use App\Http\Requests\EndPlacementRequest;
use App\Http\Requests\StorePlacementRequest;
use App\Http\Requests\TransferPlacementRequest;
use App\Http\Resources\PlacementResource;
use App\Models\Placement;
use App\Models\Student;
use App\Services\PlacementService;
use Illuminate\Http\JsonResponse;

class PlacementController extends Controller
{
    public function __construct(
        private PlacementService $placementService
    ) {
    }

    public function index(Student $student): JsonResponse
    {
        $this->authorize('viewAny', Placement::class);

        return response()->json([
            'success' => true,
            'message' => 'Placement history retrieved successfully.',
            'data' => PlacementResource::collection($this->placementService->history($student)),
        ]);
    }

    public function store(StorePlacementRequest $request, Student $student): JsonResponse
    {
        $this->authorize('create', Placement::class);

        $placement = $this->placementService->place(
            $student,
            $request->validated(),
            $request->user()
        );

        return response()->json([
            'success' => true,
            'message' => 'Student placed successfully.',
            'data' => new PlacementResource($placement),
        ], 201);
    }

    public function end(EndPlacementRequest $request, Placement $placement): JsonResponse
    {
        $this->authorize('end', $placement);

        $placement = $this->placementService->end(
            $placement,
            $request->validated()['reason']
        );

        return response()->json([
            'success' => true,
            'message' => 'Placement ended successfully.',
            'data' => new PlacementResource($placement),
        ]);
    }

    public function transfer(TransferPlacementRequest $request, Placement $placement): JsonResponse
    {
        $this->authorize('transfer', $placement);

        $newPlacement = $this->placementService->transfer(
            $placement,
            $request->validated(),
            $request->user()
        );

        return response()->json([
            'success' => true,
            'message' => 'Student transferred successfully.',
            'data' => new PlacementResource($newPlacement),
        ], 201);
    }
}