<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\User\UserController;
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

    // // Route untuk Google login
    Route::get('auth/google', [GoogleController::class, 'redirectToGoogle'])->name('google.login');
    Route::get('auth/google/callback', [GoogleController::class, 'handleGoogleCallback'])->name('google.callback');
});

Route::middleware(['auth', 'prevent.back.history'])->group(function () {

    // Route untuk profile mahasiswa
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    Route::post('/admin/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

    // Route untuk user
    Route::middleware(['role:mahasiswa'])->prefix('mahasiswa')->name('mahasiswa.')->group(function () {
        // Route untuk halaman utama user
        // Route::get('/halaman-utama', [UserController::class, 'Index'])->name('halaman-utama');

        Route::get('/cek-plagiarisme', [UserController::class, 'CekPlagiarisme'])->name('cek-plagiarisme');
        Route::get('/riwayat', [UserController::class, 'riwayatUnggahan'])->name('riwayat');
        
        // Route upload file docx
        Route::post('/cek-plagiarisme/upload', [UserController::class, 'upload'])->name('upload.file');
        Route::get('/cek-plagiarisme/document/{file_id}', [UserController::class, 'viewDocument'])->name('view.document');
        Route::get('/cek-plagiarisme/viewer/{file_id}', [UserController::class, 'showViewer'])->name('interactive.viewer');
        Route::get('/cek-plagiarisme/file/{file_id}/content', [UserController::class, 'getFileContent'])->name('get.file.content');
    });

    // Route untuk admin
    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {
            // Route::get('/admin-halaman-utama', [AdminController::class, 'Index'])->name('admin-halaman-utama');
    
            // Rute untuk CRUD Mahasiswa
            Route::get('/mahasiswa', [AdminController::class, 'indexMahasiswa'])->name('mahasiswa.index');
            Route::get('/mahasiswa/export', [AdminController::class, 'exportMahasiswa'])->name('mahasiswa.export');
            Route::get('/mahasiswa/create', [AdminController::class, 'createMahasiswa'])->name('mahasiswa.create');
            Route::post('/mahasiswa', [AdminController::class, 'storeMahasiswa'])->name('mahasiswa.store');
            Route::get('/mahasiswa/{mahasiswa}', [AdminController::class, 'showMahasiswa'])->whereNumber('mahasiswa')->name('mahasiswa.show');
            Route::get('/mahasiswa/{mahasiswa}/edit', [AdminController::class, 'editMahasiswa'])->name('mahasiswa.edit');
            Route::put('/mahasiswa/{mahasiswa}', [AdminController::class, 'updateMahasiswa'])->name('mahasiswa.update');
            Route::delete('/mahasiswa/bulk-destroy', [AdminController::class, 'bulkDestroyMahasiswa'])->name('mahasiswa.bulkDestroy');
            Route::delete('/mahasiswa/{mahasiswa}', [AdminController::class, 'destroyMahasiswa'])->name('mahasiswa.destroy');
        }
    );
});

require __DIR__.'/auth.php';