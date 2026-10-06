<?php

namespace App\Services;

use App\Models\Placement;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class PlacementService
{
    /**
     * Get paginated placements.
     */
    public function list(int $perPage = 10): LengthAwarePaginator
    {
        return Placement::query()
            ->with([
                'student',
                'department',
                'supervisor',
                'assignedBy',
            ])
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Create a new placement.
     */
    public function create(
        array $data,
        int $assignedBy
    ): Placement {
        return DB::transaction(function () use ($data, $assignedBy) {
            return Placement::create([
                'student_id' => $data['student_id'],
                'department_id' => $data['department_id'],
                'supervisor_id' => $data['supervisor_id'],
                'start_date' => $data['start_date'],
                'end_date' => $data['end_date'],
                'status' => $data['status'] ?? 'active',
                'assigned_by' => $assignedBy,
                'ended_reason' => $data['ended_reason'] ?? null,
            ])->load([
                'student',
                'department',
                'supervisor',
                'assignedBy',
            ]);
        });
    }

    /**
     * Update an existing placement.
     */
    public function update(
        Placement $placement,
        array $data
    ): Placement {
        return DB::transaction(function () use ($placement, $data) {
            $placement->update($data);

            return $placement->fresh([
                'student',
                'department',
                'supervisor',
                'assignedBy',
            ]);
        });
    }

    /**
     * Delete a placement.
     */
    public function delete(Placement $placement): void
    {
        $placement->delete();
    }
}