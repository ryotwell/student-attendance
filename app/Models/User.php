<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Notifications\ResetPasswordNotification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $guarded = [];

    protected $with = ['school'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Kirim notifikasi reset password lewat queue,
     * bukan notifikasi bawaan Laravel yang synchronous.
     */
    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function isAdmin()
    {
        return $this->role === 'ADMIN';
    }

    public function isTeacher()
    {
        return $this->role === 'GURU';
    }

    public function isTeacherBK()
    {
        return $this->role === 'GURU_BK';
    }

    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function classes()
    {
        return $this->hasMany(Xclass::class);
    }

    /**
     * Kelas yang diwalikan pada tahun ajaran yang sedang aktif saja.
     * Xclass terikat tetap ke satu academic_year_id, jadi ini dipakai
     * setiap kali "wali kelas" harus berarti wali kelas SEKARANG,
     * bukan riwayat wali kelas dari tahun ajaran manapun.
     */
    public function currentClasses()
    {
        return $this->classes()
            ->whereHas('academicYear', fn ($q) => $q->where('is_active', true))
            ->where('school_id', $this->school_id);
    }

    public function counselingCases()
    {
        return $this->hasMany(CounselingCase::class);
    }

    public function teacherDocument()
    {
        return $this->hasOne(
            TeacherDocument::class
        );
    }

    public function mySchedules()
    {
        return $this->schedules()
            ->with(['subject', 'xclass'])
            // ->where('user_id', auth()->id())
            ->orderByRaw("FIELD(day, 'MONDAY','TUESDAY','WEDNESDAY','THURSDAY','FRIDAY','SATURDAY','SUNDAY')")
            ->orderBy('start_time');
    }

    /**
     * Cek apakah user adalah wali kelas pada tahun ajaran yang sedang
     * aktif. Sebelumnya cek classes()->exists() tanpa filter tahun
     * ajaran, sehingga user yang dulu pernah jadi wali kelas (tapi
     * sudah tidak lagi di tahun ajaran ini) tetap dianggap wali kelas.
     *
     * Catatan: shortcut classes_exists (biasanya hasil withExists()
     * saat eager loading) TIDAK dipakai lagi di sini karena tidak
     * mengandung filter tahun ajaran aktif — memakainya di sini akan
     * memberi hasil yang salah. Kalau butuh versi cepat/eager-loaded,
     * pakai withExists('currentClasses') pada query pemanggil dan
     * baca $this->current_classes_exists secara eksplisit di sana.
     */
    public function isWaliKelas(): bool
    {
        return $this->currentClasses()->exists();
    }

    public function teacherAttendances(): HasMany
    {
        return $this->hasMany(TeacherAttendance::class);
    }
}