<?php

use App\Http\Controllers\AcademicYearController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\ClassController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\SubjectController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GuruBk\CounselingCaseController;
use App\Http\Controllers\Teacher\AbsensiController;
use App\Http\Controllers\Teacher\WalikelasController;
use App\Models\Xclass;
use Illuminate\Support\Facades\Auth;

// dashboard
Route::get('/', function () {
    return view('admin.dashboard', ['title' => 'E-commerce Dashboard']);
})->name('dashboard')->middleware('auth');

// students
Route::resource('students', StudentController::class)->middleware('auth');


Route::resource('classes', ClassController::class)->middleware('auth');
Route::get('/classes/{class}/schedules', [ClassController::class, 'schedules'])->name('classes.schedule')->middleware('auth');
Route::get('/classes/{class}/students', [ClassController::class, 'students'])->name('classes.students')->middleware('auth');
Route::resource('academic-years', AcademicYearController::class)->middleware('auth');
Route::resource('subjects', SubjectController::class)->middleware('auth');
Route::resource('schedules', ScheduleController::class)->middleware('auth');
Route::resource('announcements', AnnouncementController::class)->middleware('auth');

// attendance
Route::get('attendance', [AttendanceController::class, 'index'])->name('attendance.index')->middleware('auth');
Route::get('attendance/show', [AttendanceController::class, 'show'])->name('attendance.show')->middleware('auth');
Route::post('attendance', [AttendanceController::class, 'store'])->name('attendance.store')->middleware('auth');
Route::get('attendance/schedules/{class}', [AttendanceController::class, 'getSchedules'])->name('attendance.schedules')->middleware('auth');

// attendance report
Route::get('attendance/report', [AttendanceController::class, 'report'])->name('attendance.report')->middleware('auth');
Route::get('attendance/report/show', [AttendanceController::class, 'reportShow'])->name('attendance.report.show')->middleware('auth');

// attendance recap
Route::get('attendance/recap', [AttendanceController::class, 'recap'])->name('attendance.recap')->middleware('auth');
Route::get('attendance/recap/show', [AttendanceController::class, 'recapShow'])->name('attendance.recap.show')->middleware('auth');

// attendance list (daftar absensi per kelas)
Route::get('attendance/list', [AttendanceController::class, 'list'])->name('attendance.list')->middleware('auth');
Route::get('attendance/list/{class}', [AttendanceController::class, 'listShow'])->name('attendance.list.show')->middleware('auth');
Route::get('attendance/list/{class}/{schedule}/{date}/edit', [AttendanceController::class, 'listEdit'])->name('attendance.list.edit')->middleware('auth');
Route::put('attendance/list/{class}/{schedule}/{date}', [AttendanceController::class, 'listUpdate'])->name('attendance.list.update')->middleware('auth');

// absensi
Route::get('/absensi', [AbsensiController::class, 'schedules'])->name('absensi.schedules');
Route::get('/absensi/history', [AbsensiController::class, 'history'])->name('absensi.history');
Route::get('/absensi/history/{date}/{class}/{schedule}', [AbsensiController::class, 'showHistory'])->name('absensi.history.show');


Route::middleware(['auth'])->group(function () {
    Route::get('/absensi/recap', [AbsensiController::class, 'recapIndex'])
        ->name('absensi.recap');

    Route::get('/absensi/recap/{schedule}', [AbsensiController::class, 'recapShow'])
        ->name('absensi.recap.show');

    Route::get('/absensi/recap/{schedule}/export', [AbsensiController::class, 'recapExport'])
        ->name('absensi.recap.export');
});

Route::middleware(['auth'])->prefix('walikelas')->name('walikelas.')->group(function () {
    Route::get('/', [WalikelasController::class, 'index'])->name('index');
    Route::get('/{xclass}/dashboard', [WalikelasController::class, 'dashboard'])->name('dashboard');
    Route::get('/{xclass}/rekap', [WalikelasController::class, 'rekap'])->name('rekap');
    Route::get('/{xclass}/rekap/export', [WalikelasController::class, 'rekapExport'])->name('rekap.export');
});

Route::middleware(['auth'])->prefix('bk')->name('bk.')->group(function () {
    Route::get('/kasus', [CounselingCaseController::class, 'index'])->name('cases.index');
    Route::get('/kasus/tambah', [CounselingCaseController::class, 'create'])->name('cases.create');
    Route::post('/kasus', [CounselingCaseController::class, 'store'])->name('cases.store');
    Route::get('/kasus/{counselingCase}/edit', [CounselingCaseController::class, 'edit'])->name('cases.edit');
    Route::put('/kasus/{counselingCase}', [CounselingCaseController::class, 'update'])->name('cases.update');
    Route::delete('/kasus/{counselingCase}', [CounselingCaseController::class, 'destroy'])->name('cases.destroy');

    Route::get('/siswa/{student}/kasus', [CounselingCaseController::class, 'byStudent'])->name('cases.by-student');

    Route::get('/siswa/search', [CounselingCaseController::class, 'searchStudents'])->name('students.search');
});

// 

// calender pages
Route::get('/calendar', function () {
    return view('pages.calender', ['title' => 'Calendar']);
})->name('calendar');

// profile pages
Route::get('/profile', function () {
    return view('pages.profile', ['title' => 'Profile']);
})->name('profile');

// form pages
Route::get('/form-elements', function () {
    return view('pages.form.form-elements', ['title' => 'Form Elements']);
})->name('form-elements');

// tables pages
Route::get('/basic-tables', function () {
    return view('pages.tables.basic-tables', ['title' => 'Basic Tables']);
})->name('basic-tables');

// pages

Route::get('/blank', function () {
    return view('pages.blank', ['title' => 'Blank']);
})->name('blank');

// error pages
Route::get('/error-404', function () {
    return view('pages.errors.error-404', ['title' => 'Error 404']);
})->name('error-404');

// chart pages
Route::get('/line-chart', function () {
    return view('pages.chart.line-chart', ['title' => 'Line Chart']);
})->name('line-chart');

Route::get('/bar-chart', function () {
    return view('pages.chart.bar-chart', ['title' => 'Bar Chart']);
})->name('bar-chart');


// authentication pages
Route::get('/signin', function () {
    return view('pages.auth.signin', ['title' => 'Sign In']);
})->name('signin');

Route::get('/signup', function () {
    return view('pages.auth.signup', ['title' => 'Sign Up']);
})->name('signup');

// ui elements pages
Route::get('/alerts', function () {
    return view('pages.ui-elements.alerts', ['title' => 'Alerts']);
})->name('alerts');

Route::get('/avatars', function () {
    return view('pages.ui-elements.avatars', ['title' => 'Avatars']);
})->name('avatars');

Route::get('/badge', function () {
    return view('pages.ui-elements.badges', ['title' => 'Badges']);
})->name('badges');

Route::get('/buttons', function () {
    return view('pages.ui-elements.buttons', ['title' => 'Buttons']);
})->name('buttons');

Route::get('/image', function () {
    return view('pages.ui-elements.images', ['title' => 'Images']);
})->name('images');

Route::get('/videos', function () {
    return view('pages.ui-elements.videos', ['title' => 'Videos']);
})->name('videos');