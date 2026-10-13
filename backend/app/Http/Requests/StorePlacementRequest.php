<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePlacementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'student_id' => [
                'required',
                'integer',
                'exists:students,id',
            ],

            'department_id' => [
                'required',
                'integer',
                'exists:departments,id',
            ],

            'supervisor_id' => [
                'required',
                'integer',
                'exists:users,id',
            ],

            'start_date' => [
                'required',
                'date',
            ],

            'end_date' => [
                'required',
                'date',
                'after_or_equal:start_date',
            ],

            'status' => [
                'sometimes',
                Rule::in(['active', 'ended']),
            ],

            'ended_reason' => [
                'nullable',
                'string',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'student_id.required' => 'Student is required.',
            'student_id.exists' => 'The selected student does not exist.',

            'department_id.required' => 'Department is required.',
            'department_id.exists' => 'The selected department does not exist.',

            'supervisor_id.required' => 'Supervisor is required.',
            'supervisor_id.exists' => 'The selected supervisor does not exist.',

            'start_date.required' => 'Placement start date is required.',
            'start_date.date' => 'Placement start date must be a valid date.',

            'end_date.required' => 'Placement end date is required.',
            'end_date.date' => 'Placement end date must be a valid date.',
            'end_date.after_or_equal' => 'Placement end date must be on or after the start date.',

            'status.in' => 'The placement status must be active or ended.',

            'ended_reason.string' => 'The ended reason must be a valid text value.',
        ];
    }
}