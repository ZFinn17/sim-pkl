<?php

use App\Http\Controllers\Admin\AdminApplicationController;
use App\Http\Controllers\Admin\AdminAuditLogController;
use App\Http\Controllers\Admin\AdminCompanyController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminLetterController;
use App\Http\Controllers\Admin\AdminSettingController;
use App\Http\Controllers\Admin\AdminStudentController;
use App\Http\Controllers\Admin\AdminTeacherController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Company\CompanyApplicationController;
use App\Http\Controllers\Company\CompanyDashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Student\StudentApplicationController;
use App\Http\Controllers\Student\StudentCompanyCatalogController;
use App\Http\Controllers\Student\StudentDashboardController;
use App\Http\Controllers\SuratBalasanController;
use App\Http\Controllers\SuratPengantarController;
use App\Http\Controllers\VerificationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public routes
|--------------------------------------------------------------------------
*/

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);
});

/*
|--------------------------------------------------------------------------
| Verifikasi surat (publik, tidak butuh login)
|--------------------------------------------------------------------------
*/
Route::get('/verifikasi/surat/{token}', [VerificationController::class, 'show'])->name('verifikasi.surat');

/*
|--------------------------------------------------------------------------
| Authenticated routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/surat/{surat}/download', [SuratPengantarController::class, 'download'])->name('surat.download');
    Route::get('/surat-balasan/{suratBalasan}/download', [SuratBalasanController::class, 'download'])->name('surat-balasan.download');

    Route::get('/notifikasi', [NotificationController::class, 'index'])->name('notifikasi.index');
    Route::post('/notifikasi/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifikasi.read');
    Route::post('/notifikasi/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifikasi.mark-all-read');

    // ---------------- SISWA ----------------
    Route::middleware('role:siswa')->prefix('student')->name('student.')->group(function () {
        Route::get('/dashboard', [StudentDashboardController::class, 'index'])->name('dashboard');

        Route::get('/perusahaan', [StudentCompanyCatalogController::class, 'index'])->name('perusahaan.index');
        Route::get('/perusahaan/{perusahaan}', [StudentCompanyCatalogController::class, 'show'])->name('perusahaan.show');

        Route::get('/pengajuan', [StudentApplicationController::class, 'index'])->name('pengajuan.index');
        Route::get('/pengajuan/create', [StudentApplicationController::class, 'create'])->name('pengajuan.create');
        Route::post('/pengajuan', [StudentApplicationController::class, 'store'])->name('pengajuan.store');
        Route::get('/pengajuan/{pengajuan}', [StudentApplicationController::class, 'show'])->name('pengajuan.show');
        Route::post('/pengajuan/{pengajuan}/cancel', [StudentApplicationController::class, 'cancel'])->name('pengajuan.cancel');

        Route::post('/pengajuan/{pengajuan}/surat-balasan', [StudentApplicationController::class, 'uploadSuratBalasan'])->name('pengajuan.surat-balasan');
    });

    // ---------------- ADMIN ----------------
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        Route::get('/siswa', [AdminStudentController::class, 'index'])->name('siswa.index');
        Route::get('/siswa/create', [AdminStudentController::class, 'create'])->name('siswa.create');
        Route::post('/siswa', [AdminStudentController::class, 'store'])->name('siswa.store');
        Route::get('/siswa/{siswa}/edit', [AdminStudentController::class, 'edit'])->name('siswa.edit');
        Route::put('/siswa/{siswa}', [AdminStudentController::class, 'update'])->name('siswa.update');
        Route::patch('/siswa/{siswa}/toggle-active', [AdminStudentController::class, 'toggleActive'])->name('siswa.toggle-active');
        Route::post('/siswa/{siswa}/reset-password', [AdminStudentController::class, 'resetPassword'])->name('siswa.reset-password');

        Route::get('/perusahaan', [AdminCompanyController::class, 'index'])->name('perusahaan.index');
        Route::get('/perusahaan/create', [AdminCompanyController::class, 'create'])->name('perusahaan.create');
        Route::post('/perusahaan', [AdminCompanyController::class, 'store'])->name('perusahaan.store');
        Route::get('/perusahaan/{perusahaan}/edit', [AdminCompanyController::class, 'edit'])->name('perusahaan.edit');
        Route::put('/perusahaan/{perusahaan}', [AdminCompanyController::class, 'update'])->name('perusahaan.update');
        Route::patch('/perusahaan/{perusahaan}/toggle-active', [AdminCompanyController::class, 'toggleActive'])->name('perusahaan.toggle-active');

        Route::get('/guru', [AdminTeacherController::class, 'index'])->name('guru.index');
        Route::get('/guru/create', [AdminTeacherController::class, 'create'])->name('guru.create');
        Route::post('/guru', [AdminTeacherController::class, 'store'])->name('guru.store');
        Route::get('/guru/{guru}/edit', [AdminTeacherController::class, 'edit'])->name('guru.edit');
        Route::put('/guru/{guru}', [AdminTeacherController::class, 'update'])->name('guru.update');
        Route::patch('/guru/{guru}/toggle-active', [AdminTeacherController::class, 'toggleActive'])->name('guru.toggle-active');

        Route::get('/pengajuan', [AdminApplicationController::class, 'index'])->name('pengajuan.index');
        Route::get('/pengajuan/{pengajuan}', [AdminApplicationController::class, 'show'])->name('pengajuan.show');
        Route::post('/pengajuan/{pengajuan}/approve', [AdminApplicationController::class, 'approve'])->name('pengajuan.approve');
        Route::post('/pengajuan/{pengajuan}/reject', [AdminApplicationController::class, 'reject'])->name('pengajuan.reject');
        Route::post('/pengajuan/{pengajuan}/reopen', [AdminApplicationController::class, 'reopen'])->name('pengajuan.reopen');
        Route::post('/pengajuan/{pengajuan}/cancel-accepted', [AdminApplicationController::class, 'cancelAccepted'])->name('pengajuan.cancel-accepted');

        Route::get('/surat', [AdminLetterController::class, 'index'])->name('surat.index');

        Route::get('/audit-log', [AdminAuditLogController::class, 'index'])->name('audit-log.index');

        Route::get('/pengaturan', [AdminSettingController::class, 'edit'])->name('pengaturan.edit');
        Route::put('/pengaturan', [AdminSettingController::class, 'update'])->name('pengaturan.update');
    });

    // ---------------- PERUSAHAAN ----------------
    Route::middleware('role:perusahaan')->prefix('company')->name('company.')->group(function () {
        Route::get('/dashboard', [CompanyDashboardController::class, 'index'])->name('dashboard');

        Route::get('/pengajuan', [CompanyApplicationController::class, 'index'])->name('pengajuan.index');
        Route::get('/pengajuan/{pengajuan}', [CompanyApplicationController::class, 'show'])->name('pengajuan.show');
        Route::post('/pengajuan/{pengajuan}/accept', [CompanyApplicationController::class, 'accept'])->name('pengajuan.accept');
        Route::post('/pengajuan/{pengajuan}/reject', [CompanyApplicationController::class, 'reject'])->name('pengajuan.reject');
    });
});
