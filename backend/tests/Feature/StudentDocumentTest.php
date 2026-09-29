<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class StudentDocumentTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_upload_student_document(): void
    {
        Storage::fake('private');

        $admin = User::factory()->create([
            'is_active' => true,
        ]);

        $adminRole = Role::factory()->create([
            'name' => 'admin',
        ]);

        $admin->roles()->attach($adminRole->id, [
            'start_date' => now()->toDateString(),
            'end_date' => null,
        ]);

        $student = Student::factory()->create([
            'created_by' => $admin->id,
        ]);

        $file = UploadedFile::fake()->create(
            'attachment-letter.pdf',
            500,
            'application/pdf'
        );

        $response = $this->actingAs($admin, 'sanctum')
            ->post("/api/v1/students/{$student->id}/documents", [
                'document_type' => 'attachment_letter',
                'document' => $file,
            ]);

        $response->assertCreated()
            ->assertJson([
                'success' => true,
                'message' => 'Document uploaded successfully.',
            ]);

        $this->assertDatabaseHas('student_documents', [
            'student_id' => $student->id,
            'document_type' => 'attachment_letter',
            'original_name' => 'attachment-letter.pdf',
            'uploaded_by' => $admin->id,
        ]);

        $document = \App\Models\StudentDocument::first();

        Storage::disk('private')->assertExists(
            $document->stored_path
        );
    }
}