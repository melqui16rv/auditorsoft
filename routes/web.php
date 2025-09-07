<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AuditadoController;
use App\Http\Controllers\AuditorController;
use App\Http\Controllers\JefeAuditorController;
use App\Http\Controllers\SuperAdminController;

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

// Rutas para cada rol - Dashboards separados por directorio
Route::middleware('auth')->group(function () {
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
    });
});
