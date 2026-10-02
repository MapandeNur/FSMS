<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDepartmentRequest;
use App\Http\Requests\UpdateDepartmentRequest;
use App\Http\Resources\DepartmentResource;
use App\Models\Department;
use App\Services\DepartmentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function __construct(
        private DepartmentService $departmentService
    ) {
    }

    /**
     * Display a listing of departments.
     */
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Department::class);

        $perPage = min(
            max((int) $request->query('per_page', 10), 1),
            100
        );

        $departments = $this->departmentService->list($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Departments retrieved successfully.',
            'data' => DepartmentResource::collection($departments),
        ]);
    }

    /**
     * Store a newly created department.
     */
    public function store(StoreDepartmentRequest $request): JsonResponse
    {
        $this->authorize('create', Department::class);

        $department = $this->departmentService->create(
            $request->validated()
        );

        return response()->json([
            'success' => true,
            'message' => 'Department created successfully.',
            'data' => new DepartmentResource($department),
        ], 201);
    }

    /**
     * Display the specified department.
     */
    public function show(Department $department): JsonResponse
    {
        $this->authorize('view', $department);

        $department->load('headUser');

        return response()->json([
            'success' => true,
            'message' => 'Department retrieved successfully.',
            'data' => new DepartmentResource($department),
        ]);
    }

    /**
     * Update the specified department.
     */
    public function update(
        UpdateDepartmentRequest $request,
        Department $department
    ): JsonResponse {
        $this->authorize('update', $department);

        $department = $this->departmentService->update(
            $department,
            $request->validated()
        );

        return response()->json([
            'success' => true,
            'message' => 'Department updated successfully.',
            'data' => new DepartmentResource($department),
        ]);
    }

    /**
     * Remove the specified department.
     */
    public function destroy(Department $department): JsonResponse
    {
        $this->authorize('delete', $department);

        $this->departmentService->delete($department);

        return response()->json([
            'success' => true,
            'message' => 'Department deleted successfully.',
            'data' => [],
        ]);
    }
}