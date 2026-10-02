<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDepartmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                'unique:departments,name',
            ],

            'code' => [
                'required',
                'string',
                'max:50',
                'unique:departments,code',
            ],

            'description' => [
                'nullable',
                'string',
            ],

            'head_user_id' => [
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