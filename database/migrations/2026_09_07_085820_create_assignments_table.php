<?php

use App\Models\AcademicYear;
use App\Models\LessonPlan;
use App\Models\School;
use App\Models\User;
use App\Models\Xclass;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assignments', function (Blueprint $table) {
            $table->id();

            $table->string('title');
            $table->text('description');
            $table->text('attachment')->nullable();
            $table->dateTime('due_date')->nullable();

            $table->enum('status', [
                'DRAFT',
                'PUBLISHED',
                'CLOSED',
            ])->default('DRAFT');

            // Guru pembuat tugas
            $table->foreignIdFor(User::class)
                ->constrained()
                ->cascadeOnDelete();

            // Kelas tujuan
            $table->foreignIdFor(Xclass::class)
                ->constrained()
                ->cascadeOnDelete();

            // Tahun ajaran
            $table->foreignIdFor(AcademicYear::class)
                ->constrained()
                ->cascadeOnDelete();

            // Terkait rencana pembelajaran (opsional)
            $table->foreignIdFor(LessonPlan::class)
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            // Sekolah
            $table->foreignIdFor(School::class)
                ->constrained()
                ->cascadeOnDelete();

            $table->timestamps();

            $table->index([
                'user_id',
                'xclass_id',
                'academic_year_id',
                'status',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assignments');
    }
};