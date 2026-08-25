<?php

use App\Http\Controllers\AcademicYearController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\ClassController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\TeacherDocumentController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('login'))->middleware('guest');

// ADMIN
Route::middleware(['auth', 'role:ADMIN'])->prefix('admin')->group(function() {
    // dashboard
    Route::get('/', [App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');

    // students
    Route::resource('students', StudentController::class);
    Route::resource('classes', ClassController::class);
    Route::get('/classes/{class}/schedules', [ClassController::class, 'schedules'])->name('classes.schedule');
    Route::resource('academic-years', AcademicYearController::class);
    Route::resource('subjects', SubjectController::class);
    Route::resource('schedules', ScheduleController::class)->except('index', 'show');
    Route::resource('announcements', AnnouncementController::class)->except('index', 'show');

    // attendance
    Route::get('attendance', [AttendanceController::class, 'index'])->name('attendance.index');
    
    // attendance report
    Route::get('attendance/report', [AttendanceController::class, 'report'])->name('attendance.report');
    Route::get('attendance/report/show', [AttendanceController::class, 'reportShow'])->name('attendance.report.show');
    
    // attendance recap
    Route::get('attendance/recap', [AttendanceController::class, 'recap'])->name('attendance.recap');
    Route::get('attendance/recap/show', [AttendanceController::class, 'recapShow'])->name('attendance.recap.show');
    
    // attendance list (daftar absensi per kelas)
    Route::get('attendance/list', [AttendanceController::class, 'list'])->name('attendance.list');
    Route::get('attendance/list/{class}', [AttendanceController::class, 'listShow'])->name('attendance.list.show');
    Route::get('attendance/list/{class}/{schedule}/{date}/edit', [AttendanceController::class, 'listEdit'])->name('attendance.list.edit');
    Route::put('attendance/list/{class}/{schedule}/{date}', [AttendanceController::class, 'listUpdate'])->name('attendance.list.update');

    // 
    Route::get('/teacher-documents', [TeacherDocumentController::class, 'index'])
        ->name('admin.teacher-documents.index');
    Route::get('/teacher-documents/{teacherDocument}', [TeacherDocumentController::class, 'show'])
        ->name('admin.teacher-documents.show');
    Route::post('/teacher-documents/{teacherDocument}/verify', [TeacherDocumentController::class, 'verify'])
        ->name('admin.teacher-documents.verify');
    Route::post('/teacher-documents/{teacherDocument}/reject', [TeacherDocumentController::class, 'reject'])
        ->name('admin.teacher-documents.reject');
});

// GURU
Route::middleware(['auth'])->prefix('guru')->group(function() {
    Route::middleware('role:GURU')->group(function() {
        Route::get('/', App\Http\Controllers\Teacher\DashboardController::class)->name('guru.dashboard');
    
        Route::get('/absensi', [App\Http\Controllers\Teacher\AbsensiController::class, 'schedules'])
            ->name('absensi.schedules');
        Route::get('/absensi/history', [App\Http\Controllers\Teacher\AbsensiController::class, 'history'])
            ->name('absensi.history');
        Route::get('/absensi/history/{date}/{class}/{schedule}', [App\Http\Controllers\Teacher\AbsensiController::class, 'showHistory'])
            ->name('absensi.history.show');
        Route::get('/absensi/recap', [App\Http\Controllers\Teacher\AbsensiController::class, 'recapIndex'])
            ->name('absensi.recap');
        Route::get('/absensi/recap/{schedule}', [App\Http\Controllers\Teacher\AbsensiController::class, 'recapShow'])
            ->name('absensi.recap.show');
        Route::get('/absensi/recap/{schedule}/export', [App\Http\Controllers\Teacher\AbsensiController::class, 'recapExport'])
            ->name('absensi.recap.export');
    });

    Route::middleware(['role:GURU,GURU_BK'])->group(function() {
        Route::get('/documents', [App\Http\Controllers\Teacher\TeacherDocumentController::class, 'index'])
            ->name('teacher.documents.index');
        Route::get('/documents/create', [App\Http\Controllers\Teacher\TeacherDocumentController::class, 'create'])
            ->name('teacher.documents.create');
        Route::post('/documents', [App\Http\Controllers\Teacher\TeacherDocumentController::class, 'store'])
            ->name('teacher.documents.store');
        Route::get('/documents/edit', [App\Http\Controllers\Teacher\TeacherDocumentController::class, 'edit'])
            ->name('teacher.documents.edit');
        Route::put('/documents', [App\Http\Controllers\Teacher\TeacherDocumentController::class, 'update'])
            ->name('teacher.documents.update');
    });
});

// Wali Kelas
Route::middleware(['auth', 'role:GURU'])->prefix('walikelas')->name('walikelas.')->group(function () {
    Route::get('/', [App\Http\Controllers\Teacher\WalikelasController::class, 'index'])
        ->name('index');
    Route::get('/{xclass}/dashboard', [App\Http\Controllers\Teacher\WalikelasController::class, 'dashboard'])
        ->name('dashboard');
    Route::get('/{xclass}/rekap', [App\Http\Controllers\Teacher\WalikelasController::class, 'rekap'])
        ->name('rekap');
    Route::get('/{xclass}/rekap/export', [App\Http\Controllers\Teacher\WalikelasController::class, 'rekapExport'])
        ->name('rekap.export');
});

// GURU_BK
Route::middleware(['auth', 'role:GURU_BK'])->prefix('bk')->name('bk.')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\GuruBK\DashboardController::class, 'index'])->name('dashboard');

    Route::get('/kasus', [App\Http\Controllers\GuruBK\CounselingCaseController::class, 'index'])->name('cases.index');
    Route::get('/kasus/tambah', [App\Http\Controllers\GuruBK\CounselingCaseController::class, 'create'])->name('cases.create');
    Route::post('/kasus', [App\Http\Controllers\GuruBK\CounselingCaseController::class, 'store'])->name('cases.store');
    Route::get('/kasus/{counselingCase}/edit', [App\Http\Controllers\GuruBK\CounselingCaseController::class, 'edit'])->name('cases.edit');
    Route::put('/kasus/{counselingCase}', [App\Http\Controllers\GuruBK\CounselingCaseController::class, 'update'])->name('cases.update');
    Route::delete('/kasus/{counselingCase}', [App\Http\Controllers\GuruBK\CounselingCaseController::class, 'destroy'])->name('cases.destroy');

    Route::get('/siswa/{student}/kasus', [App\Http\Controllers\GuruBK\CounselingCaseController::class, 'byStudent'])->name('cases.by-student');

    Route::get('/siswa/search', [App\Http\Controllers\GuruBK\CounselingCaseController::class, 'searchStudents'])->name('students.search');
});

Route::get('/classes/{class}/students', [ClassController::class, 'students'])->name('classes.students');

Route::get('/schedules', [ScheduleController::class, 'index'])->name('schedules.index');
// Route::get('/schedules/{schedule}', [ScheduleController::class, 'show'])->name('schedules.show');

Route::get('/announcements', [AnnouncementController::class, 'index'])->name('announcements.index');
Route::get('/announcements/{announcement}', [AnnouncementController::class, 'show'])->name('announcements.show');

Route::post('attendance', [AttendanceController::class, 'store'])->name('attendance.store');
Route::get('/attendance/show', [AttendanceController::class, 'show'])->name('attendance.show')->middleware('auth');
Route::get('/attendance/schedules/{class}', [AttendanceController::class, 'getSchedules'])->name('attendance.schedules')->middleware('auth');

// // 

// // calender pages
// Route::get('/calendar', function () {
//     return view('pages.calender', ['title' => 'Calendar']);
// })->name('calendar');

// // profile pages
// Route::get('/profile', function () {
//     return view('pages.profile', ['title' => 'Profile']);
// })->name('profile');

// // form pages
// Route::get('/form-elements', function () {
//     return view('pages.form.form-elements', ['title' => 'Form Elements']);
// })->name('form-elements');

// // tables pages
// Route::get('/basic-tables', function () {
//     return view('pages.tables.basic-tables', ['title' => 'Basic Tables']);
// })->name('basic-tables');

// // pages

// Route::get('/blank', function () {
//     return view('pages.blank', ['title' => 'Blank']);
// })->name('blank');

// // error pages
// Route::get('/error-404', function () {
//     return view('pages.errors.error-404', ['title' => 'Error 404']);
// })->name('error-404');

// // chart pages
// Route::get('/line-chart', function () {
//     return view('pages.chart.line-chart', ['title' => 'Line Chart']);
// })->name('line-chart');

// Route::get('/bar-chart', function () {
//     return view('pages.chart.bar-chart', ['title' => 'Bar Chart']);
// })->name('bar-chart');


// // authentication pages
// Route::get('/signin', function () {
//     return view('pages.auth.signin', ['title' => 'Sign In']);
// })->name('signin');

// Route::get('/signup', function () {
//     return view('pages.auth.signup', ['title' => 'Sign Up']);
// })->name('signup');

// // ui elements pages
// Route::get('/alerts', function () {
//     return view('pages.ui-elements.alerts', ['title' => 'Alerts']);
// })->name('alerts');

// Route::get('/avatars', function () {
//     return view('pages.ui-elements.avatars', ['title' => 'Avatars']);
// })->name('avatars');

// Route::get('/badge', function () {
//     return view('pages.ui-elements.badges', ['title' => 'Badges']);
// })->name('badges');

// Route::get('/buttons', function () {
//     return view('pages.ui-elements.buttons', ['title' => 'Buttons']);
// })->name('buttons');

// Route::get('/image', function () {
//     return view('pages.ui-elements.images', ['title' => 'Images']);
// })->name('images');

// Route::get('/videos', function () {
//     return view('pages.ui-elements.videos', ['title' => 'Videos']);
// })->name('videos');