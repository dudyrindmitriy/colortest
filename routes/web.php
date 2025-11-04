<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\CommentToReviewController;
use App\Http\Controllers\ForumController;
use App\Http\Controllers\GeneralPageController;
use App\Http\Controllers\GenerateTestResultsController;
use App\Http\Controllers\IsaController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\PDFController;
use App\Http\Controllers\PHPMailerController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\StatisticsController;
use App\Http\Controllers\TestController;
use Illuminate\Support\Facades\Route;


Route::get('/', [GeneralPageController::class, 'showGeneralPage'])->name('home')->middleware('auth'); // Главная, доступна после авторизации

// Авторизация и регистрация
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Тест
Route::get('/test', [TestController::class, 'index'])->name('test')->middleware('auth');
Route::post('/save-result', [TestController::class, 'store'])->name('save.result')->middleware('auth');

// Профиль
Route::get('/profile', [ProfileController::class, 'index'])->name('profile')->middleware('auth');
Route::get('/result/{id}', [ProfileController::class, 'showResult'])->name('result')->middleware('auth');

// Администрирование
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin', [AdminController::class, 'index'])->name('admin.index');
    Route::get('/admin/users', [AdminController::class, 'indexUsers'])->name('admin.users.index');
    Route::get('/admin/users/{user}/edit', [AdminController::class, 'editUser'])->name('admin.users.edit');
    Route::put('/admin/users/{user}', [AdminController::class, 'updateUser'])->name('admin.users.update');
    Route::delete('/admin/users/{user}', [AdminController::class, 'destroyUser'])->name('admin.users.destroy');


    Route::get('/admin/results', [AdminController::class, 'indexResults'])->name('admin.results.index');
    Route::get('/admin/results/{result}/edit', [AdminController::class, 'editResult'])->name('admin.results.edit');
    Route::put('/admin/results/{result}', [AdminController::class, 'updateResult'])->name('admin.results.update');
    Route::delete('/admin/results/{result}', [AdminController::class, 'destroyResult'])->name('admin.results.destroy');

});
