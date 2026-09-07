<?php

use App\Models\School;
use App\Models\User;
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
        Schema::create('teacher_attendances', function (Blueprint $table) {
            $table->id();

            // Guru
            $table->foreignIdFor(User::class)
                ->constrained()
                ->cascadeOnDelete();

            // Sekolah
            $table->foreignIdFor(School::class)
                ->constrained()
                ->cascadeOnDelete();

            // Tanggal absensi
            $table->date('date');

            // Jam masuk
            $table->time('check_in')
                ->nullable();

            // Jam pulang
            $table->time('check_out')
                ->nullable();

            // Status absensi
            $table->enum('status', [
                'HADIR',
                'IZIN',
                'SAKIT',
                'ALPHA',
            ])->default('HADIR');

            // Keterangan
            $table->text('note')
                ->nullable();

            $table->timestamps();

            /*
             * Satu guru hanya boleh memiliki
             * satu absensi pada tanggal yang sama.
             */
            $table->unique([
                'user_id',
                'date',
            ]);

            /*
             * Mempercepat pencarian absensi
             * berdasarkan sekolah dan tanggal.
             */
            $table->index([
                'school_id',
                'date',
            ]);

            $table->index([
                'user_id',
                'status',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teacher_attendances');
    }
};