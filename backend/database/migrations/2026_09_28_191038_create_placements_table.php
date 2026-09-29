<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('placements', function (Blueprint $table) {
            $table->id();

            $table->foreignId('student_id')
                ->constrained('students')
                ->cascadeOnDelete();

            $table->foreignId('department_id')
                ->constrained('departments')
                ->restrictOnDelete();

            $table->foreignId('supervisor_id')
                ->constrained('users')
                ->restrictOnDelete();

            $table->date('start_date');

            $table->date('end_date');

            $table->string('status', 50)
                ->default('active')
                ->index();

            $table->foreignId('assigned_by')
                ->constrained('users')
                ->restrictOnDelete();

            $table->text('ended_reason')
                ->nullable();

            $table->timestamps();

            $table->index('student_id');
            $table->index('department_id');
            $table->index('supervisor_id');
            $table->index('assigned_by');
            $table->index([
                'student_id',
                'status',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('placements');
    }
};