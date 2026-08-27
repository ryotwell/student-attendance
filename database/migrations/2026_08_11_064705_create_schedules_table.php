<?php

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
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->string('subject_name');
            $table->enum('day', [
                'MONDAY',
                'TUESDAY',
                'WEDNESDAY',
                'THURSDAY',
                'FRIDAY',
                'SATURDAY',
                'SUNDAY',
            ]);
            $table->time('start_time');
            $table->time('end_time');
            $table->foreignIdFor(User::class);
            $table->foreignIdFor(Xclass::class);
            $table->foreignIdFor(School::class)->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};
