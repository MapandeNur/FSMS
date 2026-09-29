<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreStudentDocumentRequest;
use App\Http\Resources\StudentDocumentResource;
use App\Models\Student;
use App\Models\StudentDocument;
use App\Services\StudentDocumentService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class StudentDocumentController extends Controller
{
    public function __construct(
        private StudentDocumentService $studentDocumentService
    ) {
    }

    public function store(
        StoreStudentDocumentRequest $request,
        Student $student
    ) {
        $document = $this->studentDocumentService->upload(
            $student,
            $request->file('document'),
            $request->validated('document_type'),
            $request->user()
        );

        return response()->json([
            'success' => true,
            'message' => 'Document uploaded successfully.',
            'data' => new StudentDocumentResource($document),
        ], 201);
    }

    public function index(Request $request, Student $student)
    {
        $documents = $student->documents()
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Student documents retrieved successfully.',
            'data' => StudentDocumentResource::collection($documents),
        ]);
    }

    public function download(
        Request $request,
        StudentDocument $studentDocument
    ): StreamedResponse {
        $this->authorize('download', $studentDocument);

        return response()->streamDownload(
            function () use ($studentDocument) {
                $stream = fopen(
                    storage_path('app/private/' . $studentDocument->stored_path),
                    'rb'
                );

                fpassthru($stream);

                fclose($stream);
            },
            $studentDocument->original_name,
            [
                'Content-Type' => $studentDocument->mime_type,
            ]
        );
    }

    public function destroy(
        Request $request,
        StudentDocument $studentDocument
    ) {
        $this->authorize('delete', $studentDocument);

        $this->studentDocumentService->delete($studentDocument);

        return response()->json([
            'success' => true,
            'message' => 'Document deleted successfully.',
            'data' => [],
        ]);
    }
}