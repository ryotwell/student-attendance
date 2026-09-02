<?php

use App\Models\AcademicYear;
use App\Models\Schedule;
use App\Models\School;
use App\Models\User;
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
        Schema::create('lesson_plans', function (Blueprint $table) {
            $table->id();
            $table->string('title'); // judul/topik pembelajaran
            $table->date('date');
            $table->string('description');

            // Relasi guru
            $table->foreignIdFor(User::class)
                ->constrained()
                ->cascadeOnDelete();

            // Relasi jadwal mengajar
            $table->foreignIdFor(Schedule::class)
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            // Relasi kelas
            $table->foreignIdFor(Xclass::class)
                ->constrained()
                ->cascadeOnDelete();

            // Relasi tahun ajaran
            $table->foreignIdFor(AcademicYear::class)
                ->constrained()
                ->cascadeOnDelete();

            // Relasi sekolah
            $table->foreignIdFor(School::class)
                ->constrained()
                ->cascadeOnDelete();

            $table->timestamps();

            // Index untuk mempercepat pencarian rencana pembelajaran guru
            $table->index([
                'user_id',
                'academic_year_id',
                'xclass_id',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lesson_plans');
    }
};
