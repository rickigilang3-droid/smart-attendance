<?php

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminReportCardController;
use App\Http\Controllers\Admin\AnnouncementController;
use App\Http\Controllers\Admin\AttendanceReportController;
use App\Http\Controllers\Admin\ClassController;
use App\Http\Controllers\Admin\GateAttendanceController;
use App\Http\Controllers\Admin\LessonScheduleController;
use App\Http\Controllers\Admin\OrganizationStructureController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Admin\TeacherController;
use App\Http\Controllers\Parent\ParentDashboardController;
use App\Http\Controllers\Principal\PrincipalDashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Teacher\ClassAttendanceController;
use App\Http\Controllers\Teacher\TeacherDashboardController;
use App\Http\Controllers\Teacher\TeacherGradeController;
use Illuminate\Support\Facades\Route;

// Rute Utama
Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }

    return redirect()->route('login');
});

// Login Multi-Role
Route::get('/login/{role?}', function ($role = 'guest') {
    return view('auth.login', ['selectedRole' => $role]);
})->name('login.role');

Route::middleware(['auth'])->group(function () {

    // Redirection Dashboard Sesuai Role (Aman & Fleksibel)
    Route::get('/dashboard', function () {
        $user = auth()->user();
        $roleName = strtolower(trim($user->role->name ?? $user->role ?? ''));

        return match ($roleName) {
            'admin', 'administrator' => redirect()->route('admin.dashboard'),
            'teacher', 'guru' => redirect()->route('teacher.dashboard'),
            'principal', 'kepsek', 'kepala sekolah' => redirect()->route('principal.dashboard'),
            'parent', 'orang tua', 'orangtua', 'ortu' => redirect()->route('parent.dashboard'),
            default => abort(403),
        };
    })->name('dashboard');

    // Profile Routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Admin Routes
    Route::prefix('admin')->name('admin.')->middleware('role:admin,administrator')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        Route::resource('classes', ClassController::class);
        Route::post('/classes/{id}/homeroom-teacher', [ClassController::class, 'assignHomeroomTeacher'])->name('classes.assign-homeroom');
        Route::resource('students', StudentController::class);
        Route::resource('teachers', TeacherController::class);
        Route::resource('schedules', LessonScheduleController::class);

        Route::resource('announcements', AnnouncementController::class)->only(['index', 'store', 'destroy']);

        Route::get('/organization-structure', [OrganizationStructureController::class, 'index'])->name('organization-structure.index');
        Route::post('/organization-structure', [OrganizationStructureController::class, 'store'])->name('organization-structure.store');
        Route::delete('/organization-structure/{id}', [OrganizationStructureController::class, 'destroy'])->name('organization-structure.destroy');

        Route::get('/gate-attendance', [GateAttendanceController::class, 'index'])->name('gate-attendance.index');
        Route::post('/gate-attendance/scan', [GateAttendanceController::class, 'scan'])->name('gate-attendance.scan');

        Route::get('/reports', [AttendanceReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/pdf', [AttendanceReportController::class, 'exportPdf'])->name('reports.pdf');
        Route::get('/reports/excel', [AttendanceReportController::class, 'exportExcel'])->name('reports.excel');

        // Modul Rapor Admin (Baru Ditambahkan)
        Route::get('/report-cards', [AdminReportCardController::class, 'index'])->name('report-cards.index');
        Route::get('/report-cards/student/{id}', [AdminReportCardController::class, 'show'])->name('report-cards.show');
    });

    // Teacher Routes
    Route::prefix('teacher')->name('teacher.')->middleware('role:teacher,guru')->group(function () {
        Route::get('/dashboard', [TeacherDashboardController::class, 'index'])->name('dashboard');
        Route::post('/profile/update', [TeacherDashboardController::class, 'updateProfile'])->name('profile.update');

        Route::post('/simulate-gate', [TeacherDashboardController::class, 'simulateGateTap'])->name('simulate-gate');
        Route::post('/open-class', [TeacherDashboardController::class, 'openClass'])->name('open-class');

        Route::get('/class-attendance', [ClassAttendanceController::class, 'index'])->name('class-attendance.index');
        Route::post('/class-attendance', [ClassAttendanceController::class, 'store'])->name('class-attendance.store');
        Route::get('/class-attendance/recap', [ClassAttendanceController::class, 'recap'])->name('class-attendance.recap');

        Route::get('/attendance', [ClassAttendanceController::class, 'index'])->name('attendance.index');
        Route::post('/attendance', [ClassAttendanceController::class, 'store'])->name('attendance.store');
        Route::get('/attendance/recap', [ClassAttendanceController::class, 'recap'])->name('attendance.recap');

        // Menu Input Nilai Siswa
        Route::get('/grades', [TeacherGradeController::class, 'index'])->name('grades.index');
        Route::post('/grades', [TeacherGradeController::class, 'store'])->name('grades.store');

        Route::get('/permissions', [TeacherDashboardController::class, 'permissionsIndex'])->name('permissions.index');
        Route::post('/permission/{id}/verify', [TeacherDashboardController::class, 'verifyPermission'])->name('permission.verify');
        Route::post('/permissions/{id}/verify', [TeacherDashboardController::class, 'verifyPermission'])->name('permissions.verify');

        Route::get('/schedules', [TeacherDashboardController::class, 'schedules'])->name('schedules.index');
        Route::get('/students', [TeacherDashboardController::class, 'students'])->name('students.index');
        Route::get('/reports', [TeacherDashboardController::class, 'reports'])->name('reports.index');
        Route::get('/announcements', [TeacherDashboardController::class, 'announcements'])->name('announcements.index');
    });

    // Principal Routes
    Route::prefix('principal')->name('principal.')->middleware('role:principal,kepsek,kepala sekolah')->group(function () {
        // 1. Dashboard Supervisi
        Route::get('/dashboard', [PrincipalDashboardController::class, 'index'])->name('dashboard');

        // 2. Monitoring KBM Real-time
        Route::get('/monitoring', [PrincipalDashboardController::class, 'monitoring'])->name('monitoring');
        Route::get('/kbm', [PrincipalDashboardController::class, 'kbmMonitoring'])->name('kbm.index');

        // 3. Kedisiplinan Guru
        Route::get('/discipline', [PrincipalDashboardController::class, 'discipline'])->name('discipline');
        Route::get('/teachers-discipline', [PrincipalDashboardController::class, 'teachersDiscipline'])->name('teachers.index');

        // 4. Laporan & Rekapitulasi
        Route::get('/reports', [PrincipalDashboardController::class, 'reports'])->name('reports.index');
        Route::get('/report-list', [PrincipalDashboardController::class, 'reports'])->name('reports');
    });

    // Parent Routes
    Route::prefix('parent')->name('parent.')->middleware('role:parent,orang tua,orangtua,ortu')->group(function () {
        Route::get('/dashboard', [ParentDashboardController::class, 'index'])->name('dashboard');

        Route::post('/permission/store', [ParentDashboardController::class, 'storePermission'])->name('permission.store');
        Route::post('/permissions/store', [ParentDashboardController::class, 'storePermission'])->name('permissions.store');

        Route::get('/gate-logs', [ParentDashboardController::class, 'gateLogs'])->name('gate-logs.index');
        Route::get('/permissions', [ParentDashboardController::class, 'permissions'])->name('permissions.index');
        Route::get('/reports', [ParentDashboardController::class, 'reports'])->name('reports.index');
        Route::get('/announcements', [ParentDashboardController::class, 'announcements'])->name('announcements.index');
    });
});

require __DIR__.'/auth.php';
