<?php

use App\Models\AcademicYear;
use App\Models\Student;
use App\Models\Xclass;
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
        Schema::create('student_enrollments', function (Blueprint $table) {
            $table->id();

            $table->foreignIdFor(Student::class)
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignIdFor(AcademicYear::class)
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignIdFor(Xclass::class)
                ->constrained()
                ->cascadeOnDelete();

            $table->timestamps();

            $table->unique([
                'student_id',
                'academic_year_id'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_enrollments');
    }
};
