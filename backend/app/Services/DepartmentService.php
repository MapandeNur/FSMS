<?php

namespace App\Services;

use App\Models\Department;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class DepartmentService
{
    /**
     * Get paginated departments.
     */
    public function list(int $perPage = 10): LengthAwarePaginator
    {
        return Department::query()
            ->with('headUser')
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Create a new department.
     */
    public function create(array $data): Department
    {
        return Department::create([
            'name' => $data['name'],
            'code' => $data['code'],
            'description' => $data['description'] ?? null,
            'head_user_id' => $data['head_user_id'] ?? null,
            'is_active' => $data['is_active'] ?? true,
        ]);
    }

    /**
     * Update an existing department.
     */
    public function update(
        Department $department,
        array $data
    ): Department {
        $department->update($data);

        return $department->fresh('headUser');
    }

    /**
     * Delete a department.
     */
    public function delete(Department $department): void
    {
        $department->delete();
    }
}