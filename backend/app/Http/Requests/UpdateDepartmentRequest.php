<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDepartmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $departmentId = $this->route('department')?->id
            ?? $this->route('department');

        return [
            'name' => [
                'sometimes',
                'required',
                'string',
                'max:255',
                Rule::unique('departments', 'name')->ignore($departmentId),
            ],

            'code' => [
                'sometimes',
                'required',
                'string',
                'max:50',
                Rule::unique('departments', 'code')->ignore($departmentId),
            ],

            'description' => [
                'sometimes',
                'nullable',
                'string',
            ],

            'head_user_id' => [
                'sometimes',
                'nullable',
                'integer',
                'exists:users,id',
            ],

            'is_active' => [
                'sometimes',
                'boolean',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Department name is required.',
            'name.unique' => 'A department with this name already exists.',

            'code.required' => 'Department code is required.',
            'code.unique' => 'A department with this code already exists.',

            'head_user_id.exists' => 'The selected department head does not exist.',

            'is_active.boolean' => 'The active status must be true or false.',
        ];
    }
}