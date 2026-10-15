<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePlacementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'department_id' => ['required', 'integer', 'exists:departments,id'],
            'supervisor_id' => ['required', 'integer', 'exists:users,id'],
            'start_date' => ['required', 'date_format:Y-m-d'],
            'end_date' => ['required', 'date_format:Y-m-d', 'after_or_equal:start_date'],
        ];
    }

    public function messages(): array
    {
        return [
            'department_id.required' => 'Department is required.',
            'department_id.exists' => 'The selected department does not exist.',
            'supervisor_id.required' => 'Supervisor is required.',
            'supervisor_id.exists' => 'The selected supervisor does not exist.',
            'start_date.required' => 'Placement start date is required.',
            'end_date.required' => 'Placement end date is required.',
            'end_date.after_or_equal' => 'Placement end date must be on or after the start date.',
        ];
    }
}