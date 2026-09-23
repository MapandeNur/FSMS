<?php

namespace App\Http\Controllers;

use App\Http\Resources\StudentResource;
use App\Models\Student;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class StudentController extends Controller
{
    /**
     * Display a paginated list of students.
     * Supports search by name or registration number
     * and filtering by status.
     */
    public function index(Request $request): JsonResponse
    {
        $search = $request->query('search');
        $status = $request->query('status');

        $students = Student::with('creator')
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('first_name', 'like', "%{$search}%")
                        ->orWhere('middle_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere(
                            'registration_number',
                            'like',
                            "%{$search}%"
                        );
                });
            })
            ->when($status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return response()->json([
            'success' => true,
            'message' => 'Students retrieved successfully.',
            'data' => StudentResource::collection($students),
            'errors' => null,
        ]);
    }

    /**
     * Store a newly created student.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'registration_number' => [
                'nullable',
                'string',
                'max:50',
                'uppercase',
                'regex:/^FS\/\d{4}\/\d{4}$/',
                'unique:students,registration_number',
            ],

            'first_name' => [
                'required',
                'string',
                'max:50',
                'regex:/^[A-Za-zÀ-ÿ]+(?:[\'-][A-Za-zÀ-ÿ]+)*$/',
            ],

            'middle_name' => [
                'nullable',
                'string',
                'max:50',
                'regex:/^[A-Za-zÀ-ÿ]+(?:[\'-][A-Za-zÀ-ÿ]+)*$/',
            ],

            'last_name' => [
                'required',
                'string',
                'max:50',
                'regex:/^[A-Za-zÀ-ÿ]+(?:[\'-][A-Za-zÀ-ÿ]+)*$/',
            ],

            'gender' => [
                'required',
                Rule::in(['male', 'female', 'other']),
            ],

            'date_of_birth' => [
                'required',
                'date',
                'before:today',
                'before_or_equal:' . now()->subYears(16)->toDateString(),
            ],

            'email' => [
                'required',
                'email',
                'max:255',
                'unique:students,email',
            ],

            'phone' => [
                'required',
                'string',
                'regex:/^(?:\+255|0)[67]\d{8}$/',
                'unique:students,phone',
            ],

            'institution_name' => [
                'required',
                'string',
                'max:255',
            ],

            'programme_of_study' => [
                'required',
                'string',
                'max:255',
            ],

            'year_of_study' => [
                'required',
                'integer',
                'min:1',
                'max:7',
            ],

            'start_date' => [
                'required',
                'date',
            ],

            'end_date' => [
                'required',
                'date',
                'after:start_date',
            ],

            'status' => [
                'nullable',
                Rule::in(['active', 'completed', 'inactive']),
            ],
        ]);

        $student = DB::transaction(function () use ($validated, $request) {

            if (empty($validated['registration_number'])) {
                $validated['registration_number']
                    = $this->generateRegistrationNumber();
            }

            $validated['status'] = $validated['status'] ?? 'active';

            $validated['created_by'] = $request->user()->id;

            return Student::create($validated);
        });

        $student->load('creator');

        return response()->json([
            'success' => true,
            'message' => 'Student registered successfully.',
            'data' => new StudentResource($student),
            'errors' => null,
        ], 201);
    }

    /**
     * Display the specified student.
     */
    public function show(Student $student): JsonResponse
    {
        $student->load('creator');

        return response()->json([
            'success' => true,
            'message' => 'Student retrieved successfully.',
            'data' => new StudentResource($student),
            'errors' => null,
        ]);
    }

    /**
     * Update the specified student.
     */
    public function update(Request $request, Student $student): JsonResponse
    {
        $validated = $request->validate([
            'registration_number' => [
                'sometimes',
                'required',
                'string',
                'max:50',
                'uppercase',
                'regex:/^FS\/\d{4}\/\d{4}$/',
                Rule::unique('students', 'registration_number')
                    ->ignore($student->id),
            ],

            'first_name' => [
                'sometimes',
                'required',
                'string',
                'max:50',
                'regex:/^[A-Za-zÀ-ÿ]+(?:[\'-][A-Za-zÀ-ÿ]+)*$/',
            ],

            'middle_name' => [
                'nullable',
                'string',
                'max:50',
                'regex:/^[A-Za-zÀ-ÿ]+(?:[\'-][A-Za-zÀ-ÿ]+)*$/',
            ],

            'last_name' => [
                'sometimes',
                'required',
                'string',
                'max:50',
                'regex:/^[A-Za-zÀ-ÿ]+(?:[\'-][A-Za-zÀ-ÿ]+)*$/',
            ],

            'gender' => [
                'sometimes',
                'required',
                Rule::in(['male', 'female', 'other']),
            ],

            'date_of_birth' => [
                'sometimes',
                'required',
                'date',
                'before:today',
                'before_or_equal:' . now()->subYears(16)->toDateString(),
            ],

            'email' => [
                'sometimes',
                'required',
                'email',
                'max:255',
                Rule::unique('students', 'email')
                    ->ignore($student->id),
            ],

            'phone' => [
                'sometimes',
                'required',
                'string',
                'regex:/^(?:\+255|0)[67]\d{8}$/',
                Rule::unique('students', 'phone')
                    ->ignore($student->id),
            ],

            'institution_name' => [
                'sometimes',
                'required',
                'string',
                'max:255',
            ],

            'programme_of_study' => [
                'sometimes',
                'required',
                'string',
                'max:255',
            ],

            'year_of_study' => [
                'sometimes',
                'required',
                'integer',
                'min:1',
                'max:7',
            ],

            'start_date' => [
                'sometimes',
                'required',
                'date',
            ],

            'end_date' => [
                'sometimes',
                'required',
                'date',
                'after:start_date',
            ],

            'status' => [
                'sometimes',
                'required',
                Rule::in(['active', 'completed', 'inactive']),
            ],
        ]);

        $student->update($validated);

        $student->fresh()->load('creator');

        return response()->json([
            'success' => true,
            'message' => 'Student updated successfully.',
            'data' => new StudentResource($student),
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
     * Generate a unique registration number for the current year.
     */
    private function generateRegistrationNumber(): string
    {
        $year = now()->year;

        $lastStudent = Student::withTrashed()
            ->where(
                'registration_number',
                'like',
                "FS/{$year}/%"
            )
            ->orderByDesc('registration_number')
            ->lockForUpdate()
            ->first();

        $nextNumber = 1;

        if ($lastStudent) {
            $lastNumber = (int) substr(
                $lastStudent->registration_number,
                -4
            );

            $nextNumber = $lastNumber + 1;
        }

        return sprintf(
            'FS/%d/%04d',
            $year,
            $nextNumber
        );
    }
}