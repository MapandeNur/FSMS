<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {

            $table->id();

            $table->string('registration_number', 50)->unique();

            $table->string('first_name', 100);

            $table->string('middle_name', 100)->nullable();

            $table->string('last_name', 100);

            $table->string('gender', 20);

            $table->date('date_of_birth');

            $table->string('email')->unique();

            $table->string('phone', 20)->unique();

            $table->string('institution_name');

            $table->string('programme_of_study');

            $table->unsignedTinyInteger('year_of_study');

            $table->date('start_date');

            $table->date('end_date');

            $table->string('status', 50)
                ->default('active');

            $table->foreignId('created_by')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->timestamps();

            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};