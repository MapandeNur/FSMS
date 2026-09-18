<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class StudentController extends Controller
{
    /**
     * Display a listing of students.
     */
    public function index(): JsonResponse
    {
        $students = Student::with('creator')
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Students retrieved successfully.',
            'data' => $students,
            'errors' => null,
        ]);
    }

    /**
     * Store a newly created student.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'middle_name' => ['nullable', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'gender' => ['required', 'string', 'max:20'],
            'date_of_birth' => ['required', 'date', 'before:today'],
            'email' => ['required', 'email', 'max:255', 'unique:students,email'],
            'phone' => ['required', 'string', 'max:20', 'unique:students,phone'],
            'institution_name' => ['required', 'string', 'max:255'],
            'programme_of_study' => ['required', 'string', 'max:255'],
            'year_of_study' => ['required', 'integer', 'min:1', 'max:10'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'status' => ['nullable', 'string', 'max:50'],
        ]);

        $validated['registration_number'] = $this->generateRegistrationNumber();
        $validated['status'] = $validated['status'] ?? 'active';
        $validated['created_by'] = $request->user()->id;

        $student = Student::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Student registered successfully.',
            'data' => $student->load('creator'),
            'errors' => null,
        ], 201);
    }

    /**
     * Display the specified student.
     */
    public function show(Student $student): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Student retrieved successfully.',
            'data' => $student->load('creator'),
            'errors' => null,
        ]);
    }

    /**
     * Update the specified student.
     */
    public function update(Request $request, Student $student): JsonResponse
    {
        $validated = $request->validate([
            'first_name' => ['sometimes', 'required', 'string', 'max:100'],
            'middle_name' => ['nullable', 'string', 'max:100'],
            'last_name' => ['sometimes', 'required', 'string', 'max:100'],
            'gender' => ['sometimes', 'required', 'string', 'max:20'],
            'date_of_birth' => ['sometimes', 'required', 'date', 'before:today'],
            'email' => [
                'sometimes',
                'required',
                'email',
                'max:255',
                Rule::unique('students', 'email')->ignore($student->id),
            ],
            'phone' => [
                'sometimes',
                'required',
                'string',
                'max:20',
                Rule::unique('students', 'phone')->ignore($student->id),
            ],
            'institution_name' => ['sometimes', 'required', 'string', 'max:255'],
            'programme_of_study' => ['sometimes', 'required', 'string', 'max:255'],
            'year_of_study' => ['sometimes', 'required', 'integer', 'min:1', 'max:10'],
            'start_date' => ['sometimes', 'required', 'date'],
            'end_date' => ['sometimes', 'required', 'date', 'after_or_equal:start_date'],
            'status' => ['sometimes', 'required', 'string', 'max:50'],
        ]);

        $student->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Student updated successfully.',
            'data' => $student->fresh()->load('creator'),
            'errors' => null,
        ]);
    }

    /**
     * Remove the specified student.
     */
    public function destroy(Student $student): JsonResponse
    {
        $student->delete();

        return response()->json([
            'success' => true,
            'message' => 'Student deleted successfully.',
            'data' => null,
            'errors' => null,
        ]);
    }

    /**
     * Generate the next student registration number.
     */
    private function generateRegistrationNumber(): string
    {
        $year = now()->year;

        $lastStudent = Student::withTrashed()
            ->where('registration_number', 'like', "FS/{$year}/%")
            ->orderByDesc('id')
            ->first();

        $nextNumber = 1;

        if ($lastStudent) {
            $lastNumber = (int) substr($lastStudent->registration_number, -4);
            $nextNumber = $lastNumber + 1;
        }

        return sprintf('FS/%d/%04d', $year, $nextNumber);
    }
}