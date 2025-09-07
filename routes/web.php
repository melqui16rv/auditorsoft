<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AuditadoController;
use App\Http\Controllers\AuditorController;
use App\Http\Controllers\JefeAuditorController;
use App\Http\Controllers\SuperAdminController;
use App\Http\Controllers\SuperAdmin\UserController;
use App\Http\Controllers\ProfileController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Redirigir la raíz al login
Route::get('/', function () {
    return redirect('/login');
})->name('home');

// Rutas de autenticación
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Rutas de restablecimiento de contraseña (públicas)
Route::post('/password/email', [ProfileController::class, 'requestPasswordReset'])->name('password.email');
Route::get('/password/reset/{token}', [ProfileController::class, 'showResetForm'])->name('password.reset');
Route::post('/password/reset', [ProfileController::class, 'resetPassword'])->name('password.update');

// Rutas protegidas con autenticación
Route::middleware('auth')->group(function () {
    // Rutas de perfil (disponibles para todos los usuarios autenticados)
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'show'])->name('show');
        Route::put('/update', [ProfileController::class, 'updateProfile'])->name('update');
        Route::put('/password', [ProfileController::class, 'changePassword'])->name('password');
    });

    // Rutas del Auditado
    Route::prefix('auditado')->name('auditado.')->group(function () {
        Route::get('/dashboard', [AuditadoController::class, 'dashboard'])->name('dashboard');
    });

    // Rutas del Auditor
    Route::prefix('auditor')->name('auditor.')->group(function () {
        Route::get('/dashboard', [AuditorController::class, 'dashboard'])->name('dashboard');
    });

    // Rutas del Jefe Auditor
    Route::prefix('jefe-auditor')->name('jefe-auditor.')->group(function () {
        Route::get('/dashboard', [JefeAuditorController::class, 'dashboard'])->name('dashboard');
    });

    // Rutas del Super Administrador
    Route::prefix('super-admin')->name('super-admin.')->group(function () {
        Route::get('/dashboard', [SuperAdminController::class, 'dashboard'])->name('dashboard');
        
        // Gestión de Usuarios
        Route::resource('users', UserController::class)->except(['create', 'store', 'show', 'edit', 'update', 'destroy']);
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');
        Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
        Route::patch('/users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
        Route::put('/users/{user}/update-password', [UserController::class, 'updatePassword'])->name('users.update-password');
    });
});
