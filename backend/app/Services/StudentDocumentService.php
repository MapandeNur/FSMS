<?php

namespace App\Services;

use App\Models\Student;
use App\Models\StudentDocument;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class StudentDocumentService
{
    public function upload(
        Student $student,
        UploadedFile $file,
        string $documentType,
        User $uploadedBy
    ): StudentDocument {
        $extension = strtolower($file->getClientOriginalExtension());

        $filename = Str::uuid()->toString() . '.' . $extension;

        $storedPath = $file->storeAs(
            'student-documents/' . $student->id,
            $filename,
            'private'
        );

        return StudentDocument::create([
            'student_id' => $student->id,
            'document_type' => $documentType,
            'original_name' => $file->getClientOriginalName(),
            'stored_path' => $storedPath,
            'mime_type' => $file->getMimeType(),
            'size_bytes' => $file->getSize(),
            'uploaded_by' => $uploadedBy->id,
        ]);
    }

    public function delete(StudentDocument $document): void
    {
        if ($document->stored_path) {
            Storage::disk('private')->delete($document->stored_path);
        }

        $document->delete();
    }
}