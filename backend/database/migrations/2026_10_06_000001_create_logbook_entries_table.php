[06:13, 10/12/2026] N•o•o•r 👨🏼‍🦲: <?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('logbook_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->unsignedBigInteger('placement_id');
            $table->index('placement_id');
            $table->date('entry_date');
            $table->text('activities');
            $table->text('skills_learned')->nullable();
            $table->text('challenges')->nullable();
            $table->decimal('hours_worked', 4, 2);
            $table->string('status')->default('draft');
            $table->text('supervisor_comment')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('reviewed_at')->nullable();
            $table->dateTime('submitted_at')->nullable();
            $table->timestamps();

            $table->unique(['student_id', 'entry_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('logbook_entries');
    }
};
