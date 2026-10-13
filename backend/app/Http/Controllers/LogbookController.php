<?php

namespace App\Http\Controllers;

use App\Models\LogbookEntry;
use App\Models\Student;
use App\Services\LogbookService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LogbookController extends Controller
{
    protected LogbookService $service;

    public function __construct(LogbookService $service)
    {
        $this->service = $service;
    }

    protected function student(Request $request): Student
    {
        return Student::where('email', $request->user()->email)->firstOrFail();
    }

    public function index(Request $request): JsonResponse
    {
        $student = $this->student($request);

        $entries = LogbookEntry::where('student_id', $student->id)
            ->orderByDesc('entry_date')
            ->paginate(15);

        return response()->json($entries);
    }

    public function store(Request $request): JsonResponse
    {
        $student = $this->student($request);

        $data = $request->validate([
            'entry_date' => ['required', 'date'],
            'activities' => ['required', 'string'],
            'skills_learned' => ['nullable', 'string'],
            'challenges' => ['nullable', 'string'],
            'hours_worked' => ['required', 'numeric'],
        ]);

        $entry = $this->service->create($student, $data);

        return response()->json(['data' => $entry], 201);
    }

    public function update(Request $request, LogbookEntry $logbook): JsonResponse
    {
        $student = $this->student($request);

        if ($logbook->student_id !== $student->id) {
            abort(404);
        }

        $data = $request->validate([
            'entry_date' => ['sometimes', 'date'],
            'activities' => ['sometimes', 'string'],
            'skills_learned' => ['nullable', 'string'],
            'challenges' => ['nullable', 'string'],
            'hours_worked' => ['sometimes', 'numeric'],
        ]);

        $entry = $this->service->update($logbook, $data);

        return response()->json(['data' => $entry]);
    }

    public function submit(Request $request, LogbookEntry $logbook): JsonResponse
    {
        $student = $this->student($request);

        if ($logbook->student_id !== $student->id) {
            abort(404);
        }

        $entry = $this->service->submit($logbook);

        return response()->json(['data' => $entry]);
    }
}