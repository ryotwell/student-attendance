<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\TeacherAttendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TeacherAttendanceController extends Controller
{
    /**
     * Halaman absensi guru.
     */
    public function index()
    {
        $user = Auth::user();

        /*
         * Ambil absensi hari ini.
         */
        $todayAttendance = TeacherAttendance::query()
            ->where('user_id', $user->id)
            ->where('date', today())
            ->first();

        /*
         * Ambil riwayat absensi.
         */
        $attendances = TeacherAttendance::query()
            ->where('user_id', $user->id)
            ->latest('date')
            ->paginate(10);

        return view('teacher.attendance.index', [
            'todayAttendance' => $todayAttendance,
            'attendances' => $attendances,
        ]);
    }

    /**
     * Check in guru.
     */
    public function checkIn(Request $request)
    {
        $user = Auth::user();

        /*
         * Pastikan user adalah guru.
         */
        if ($user->role !== 'GURU') {
            abort(403, 'Halaman ini hanya dapat digunakan oleh guru.');
        }

        /*
         * Pastikan guru memiliki sekolah.
         */
        if (!$user->school_id) {
            return back()->withErrors([
                'attendance' =>
                    'Akun guru belum terhubung dengan sekolah.',
            ]);
        }

        $data = $request->validate([
            'status' => [
                'required',
                'in:HADIR,IZIN,SAKIT,ALPHA',
            ],

            'note' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ]);

        /*
         * Cari absensi hari ini.
         */
        $attendance = TeacherAttendance::query()
            ->where('user_id', $user->id)
            ->whereDate('date', today())
            ->first();

        /*
         * Jika sudah ada absensi hari ini.
         */
        if ($attendance) {
            return back()->withErrors([
                'attendance' =>
                    'Anda sudah melakukan absensi hari ini.',
            ]);
        }

        /*
         * Untuk status HADIR,
         * simpan waktu check-in.
         *
         * Untuk IZIN/SAKIT/ALPHA,
         * tidak perlu check-in.
         */
        $checkIn = null;

        if ($data['status'] === 'HADIR') {
            $checkIn = now()->format('H:i:s');
        }

        TeacherAttendance::create([
            'user_id' => $user->id,
            'school_id' => $user->school_id,
            'date' => today(),
            'check_in' => $checkIn,
            'check_out' => null,
            'status' => $data['status'],
            'note' => $data['note'] ?? null,
        ]);

        return back()->with(
            'success',
            'Absensi berhasil dicatat.'
        );
    }

    /**
     * Check out guru.
     */
    public function checkOut()
    {
        $user = Auth::user();

        /*
         * Pastikan user adalah guru.
         */
        if ($user->role !== 'GURU') {
            abort(403, 'Halaman ini hanya dapat digunakan oleh guru.');
        }

        /*
         * Ambil absensi hari ini.
         */
        $attendance = TeacherAttendance::query()
            ->where('user_id', $user->id)
            ->whereDate('date', today())
            ->first();

        /*
         * Belum melakukan absensi.
         */
        if (!$attendance) {
            return back()->withErrors([
                'attendance' =>
                    'Anda belum melakukan check-in hari ini.',
            ]);
        }

        /*
         * Hanya status HADIR yang dapat check-out.
         */
        if ($attendance->status !== 'HADIR') {
            return back()->withErrors([
                'attendance' =>
                    'Check-out hanya dapat dilakukan untuk status Hadir.',
            ]);
        }

        /*
         * Sudah check-out.
         */
        if ($attendance->check_out) {
            return back()->withErrors([
                'attendance' =>
                    'Anda sudah melakukan check-out hari ini.',
            ]);
        }

        $attendance->update([
            'check_out' => now()->format('H:i:s'),
        ]);

        return back()->with(
            'success',
            'Check-out berhasil dicatat.'
        );
    }

    /**
     * Detail riwayat absensi guru.
     */
    public function show(TeacherAttendance $teacherAttendance)
    {
        $user = Auth::user();

        /*
         * Guru hanya dapat melihat
         * absensinya sendiri.
         */
        if (
            $user->role === 'GURU' &&
            $teacherAttendance->user_id !== $user->id
        ) {
            abort(
                403,
                'Anda tidak memiliki akses ke absensi ini.'
            );
        }

        /*
         * User selain SUPERADMIN hanya boleh
         * melihat data dari sekolahnya.
         */
        if (
            $user->role !== 'SUPERADMIN' &&
            $teacherAttendance->school_id !== $user->school_id
        ) {
            abort(
                403,
                'Anda tidak memiliki akses ke absensi ini.'
            );
        }

        $teacherAttendance->load('user');

        return view(
            'teacher.attendance.show',
            compact('teacherAttendance')
        );
    }
}