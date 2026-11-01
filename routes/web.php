<?php

use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\CandidateController as AdminCandidateController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DepartmentStudentController;
use App\Http\Controllers\Admin\ElectionController;
use App\Http\Controllers\Admin\PositionController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\VoterController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\CandidateController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\VoteController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Student & Guest Routes
|--------------------------------------------------------------------------
*/
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/candidates', [CandidateController::class, 'index'])->name('candidates');
Route::get('/candidates/{candidate}', [CandidateController::class, 'show'])->name('candidates.show');
Route::get('/results', [CandidateController::class, 'index'])->name('results');

/*
|--------------------------------------------------------------------------
| Public Student Authentication Routes
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function (): void {
    Route::get('/register', [RegisterController::class, 'create'])->name('register');
    Route::post('/register', [RegisterController::class, 'store']);

    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);

    Route::get('/forgot-password', [PasswordResetController::class, 'create'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetController::class, 'store'])->name('password.email');

    Route::get('/reset-password/{token}', [PasswordResetController::class, 'edit'])->name('password.reset');
    Route::post('/reset-password', [PasswordResetController::class, 'update'])->name('password.update');
});

/*
|--------------------------------------------------------------------------
| Authenticated Student Routes
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function (): void {
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    // Email Verification Routes
    Route::get('/email/verify', [EmailVerificationController::class, 'notice'])->name('verification.notice');
    Route::post('/email/verify', [EmailVerificationController::class, 'verifyCode'])->name('verification.code');
    Route::post('/email/update', [EmailVerificationController::class, 'updateEmail'])->name('verification.update_email');
    Route::get('/email/verify/{id}/{hash}', [EmailVerificationController::class, 'verify'])
        ->middleware('signed')
        ->name('verification.verify');
    Route::post('/email/verification-notification', [EmailVerificationController::class, 'resend'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    // Protected Voting Routes (Strictly requires both authentication and email verification)
    Route::get('/vote', [VoteController::class, 'index'])
        ->middleware('verified')
        ->name('vote');
    Route::post('/vote', [VoteController::class, 'store'])
        ->middleware('verified')
        ->name('vote.store');
});

/*
|--------------------------------------------------------------------------
| Dedicated Administration Routes
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->group(function (): void {
    // Separate Admin Login
    Route::get('/login', [AdminAuthController::class, 'create'])->name('login');
    Route::post('/login', [AdminAuthController::class, 'store']);
    Route::post('/logout', [AdminAuthController::class, 'destroy'])->name('logout');

    // Protected Administrative Console (Requires auth and admin role)
    Route::middleware(['auth', 'admin'])->group(function (): void {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        // Voter Management
        Route::get('/voters', [VoterController::class, 'index'])->name('voters.index');
        Route::post('/voters/{user}/toggle-suspension', [VoterController::class, 'toggleSuspension'])->name('voters.toggle-suspension');

        // Department Matric Eligibility Register
        Route::get('/department-students', [DepartmentStudentController::class, 'index'])->name('department-students.index');
        Route::post('/department-students/import', [DepartmentStudentController::class, 'import'])->name('department-students.import');
        Route::get('/department-students/template', [DepartmentStudentController::class, 'template'])->name('department-students.template');

        // Position Management
        Route::get('/positions', [PositionController::class, 'index'])->name('positions.index');
        Route::post('/positions', [PositionController::class, 'store'])->name('positions.store');
        Route::put('/positions/{position}', [PositionController::class, 'update'])->name('positions.update');
        Route::delete('/positions/{position}', [PositionController::class, 'destroy'])->name('positions.destroy');

        // Position Details & Candidate Management
        Route::get('/positions/{position}', [AdminCandidateController::class, 'show'])->name('positions.show');
        Route::post('/positions/{position}/candidates', [AdminCandidateController::class, 'store'])->name('candidates.store');
        Route::put('/candidates/{candidate}', [AdminCandidateController::class, 'update'])->name('candidates.update');
        Route::delete('/candidates/{candidate}', [AdminCandidateController::class, 'destroy'])->name('candidates.destroy');

        // Election Governance & Results Visibility
        Route::get('/election', [ElectionController::class, 'index'])->name('election.index');
        Route::put('/election', [ElectionController::class, 'update'])->name('election.update');
        Route::post('/election/toggle-status', [ElectionController::class, 'toggleStatus'])->name('election.toggle-status');
        Route::post('/election/toggle-results', [ElectionController::class, 'toggleResults'])->name('election.toggle-results');

        // System Settings (Logo, Favicon & SMTP Email)
        Route::get('/settings', SettingController::class.'@index')->name('settings.index');
        Route::post('/settings', SettingController::class.'@update')->name('settings.update');
        Route::post('/settings/test-email', SettingController::class.'@testEmail')->name('settings.test-email');
    });
});
