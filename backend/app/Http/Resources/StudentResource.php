<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'registration_number' => $this->registration_number,

            'first_name' => $this->first_name,
            'middle_name' => $this->middle_name,
            'last_name' => $this->last_name,

            'gender' => $this->gender,
            'date_of_birth' => $this->date_of_birth,

            'email' => $this->email,
            'phone' => $this->phone,

            'institution_name' => $this->institution_name,
            'programme_of_study' => $this->programme_of_study,
            'year_of_study' => $this->year_of_study,

            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'status' => $this->status,

            'created_by' => $this->created_by,

            'creator' => $this->whenLoaded('creator', function () {
                return [
                    'id' => $this->creator->id,
                    'name' => $this->creator->name,
                    'email' => $this->creator->email,
                ];
            }),

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}