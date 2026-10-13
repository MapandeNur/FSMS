<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PlacementResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            'student' => $this->whenLoaded('student', function () {
                return [
                    'id' => $this->student->id,
                    'registration_number' => $this->student->registration_number,
                    'name' => trim(
                        $this->student->first_name . ' ' .
                        ($this->student->middle_name ?? '') . ' ' .
                        $this->student->last_name
                    ),
                ];
            }),

            'department' => $this->whenLoaded('department', function () {
                return [
                    'id' => $this->department->id,
                    'name' => $this->department->name,
                    'code' => $this->department->code,
                ];
            }),

            'supervisor' => $this->whenLoaded('supervisor', function () {
                return [
                    'id' => $this->supervisor->id,
                    'name' => $this->supervisor->name,
                    'email' => $this->supervisor->email,
                    'phone' => $this->supervisor->phone,
                ];
            }),

            'start_date' => $this->start_date?->toDateString(),
            'end_date' => $this->end_date?->toDateString(),
            'status' => $this->status?->value,

            'assigned_by' => $this->whenLoaded('assignedBy', function () {
                return [
                    'id' => $this->assignedBy->id,
                    'name' => $this->assignedBy->name,
                ];
            }),

            'ended_reason' => $this->ended_reason,

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}