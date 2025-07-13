<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\User\UserHalamanController\UserHalamanController;
use App\Http\Controllers\Admin\AdminHalamanController\AdminHalamanController;
use App\Http\Controllers\Auth\AdminAuthController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\CekPlagiarismeController;
use App\Http\Controllers\FileUploadController;
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

Route::middleware('guest')->group(function () {
    Route::get('/', function () {
        return redirect('/login');
    });

    // Route untuk admin login
    Route::get('/admin/login', [AdminAuthController::class, 'showLoginForm'])->name('admin.login');
    Route::post('/admin/login', [AdminAuthController::class, 'login']);

    // Route untuk Google login
    Route::get('auth/google', [GoogleController::class, 'redirectToGoogle'])->name('google.login');
    Route::get('auth/google/callback', [GoogleController::class, 'handleGoogleCallback'])->name('google.callback');
});

Route::post('/admin/logout', [AdminAuthController::class, 'logout'])->name('admin.logout')->middleware('auth');

Route::middleware(['auth', 'prevent.back.history'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Route untuk user dan admin
    Route::get('/cek-plagiarisme', [CekPlagiarismeController::class, 'CekPlagiarisme'])
        ->middleware('role:mahasiswa,admin')
        ->name('cek-plagiarisme');
    Route::post('/cek-plagiarisme/upload', [CekPlagiarismeController::class, 'upload'])
        ->middleware('role:mahasiswa,admin')
        ->name('upload.file');
    Route::get('/cek-plagiarisme/document/{file_id}', [CekPlagiarismeController::class, 'viewDocument'])
        ->middleware('role:mahasiswa,admin')
        ->name('view.document');
    Route::get('/cek-plagiarisme/viewer/{file_id}', [CekPlagiarismeController::class, 'showViewer'])
        ->middleware('role:mahasiswa,admin')
        ->name('interactive.viewer');
    Route::get('/cek-plagiarisme/file/{file_id}/content', [CekPlagiarismeController::class, 'getFileContent'])
        ->middleware('role:mahasiswa,admin')
        ->name('get.file.content');

    // Route untuk user
    Route::middleware(['role:mahasiswa'])->group(function () {
        // Route untuk halaman utama user
        Route::get('/halaman-utama', [UserHalamanController::class, 'Index'])->name('halaman-utama');
    });

    // Route untuk admin
    Route::middleware(['role:admin'])->group(function () {
        Route::get('/admin-halaman-utama', [AdminHalamanController::class, 'Index'])->name('admin-halaman-utama');
    });
});

require __DIR__.'/auth.php';
