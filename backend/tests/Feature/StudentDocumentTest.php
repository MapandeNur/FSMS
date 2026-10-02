<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\Student;
use App\Models\StudentDocument;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class StudentDocumentTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_upload_student_document(): void
    {
        Storage::fake('private');

        $admin = User::create([
            'name' => 'Test Admin',
            'email' => 'admin-test@fsms.com',
            'phone' => '0711111111',
            'password' => 'Password123',
            'is_active' => true,
        ]);

        $adminRole = Role::create([
            'name' => 'admin',
            'description' => 'System administrator',
        ]);

        $admin->roles()->attach($adminRole->id, [
            'start_date' => now()->toDateString(),
            'end_date' => null,
        ]);

        $student = Student::create([
            'registration_number' => 'TEST-STU-001',
            'first_name' => 'Test',
            'middle_name' => null,
            'last_name' => 'Student',
            'gender' => 'male',
            'date_of_birth' => '2003-01-15',
            'email' => 'student-test@fsms.com',
            'phone' => '0722222222',
            'institution_name' => 'Institute of Finance Management',
            'programme_of_study' => 'Bachelor of Information Technology',
            'year_of_study' => 2,
            'start_date' => '2026-09-01',
            'end_date' => '2026-11-01',
            'status' => 'active',
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

        $document = StudentDocument::firstOrFail();

        Storage::disk('private')->assertExists(
            $document->stored_path
        );
    }
}