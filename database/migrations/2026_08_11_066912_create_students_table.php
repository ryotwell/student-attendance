<?php

use App\Models\School;
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
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('nis');
            $table->string('nisn');
            $table->enum('gender', ['MALE', 'FEMALE']);
            $table->enum('status', ['AKTIF', 'LULUS', 'PINDAH', 'KELUAR'])->default('AKTIF');
            $table->string('parent_name')->nullable();
            $table->string('parent_phone')->nullable();
            $table->foreignIdFor(School::class)->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
