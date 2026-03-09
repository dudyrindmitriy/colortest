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
use App\Http\Controllers\PackageController;
use App\Http\Controllers\PDFController;
use App\Http\Controllers\PHPMailerController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\StatisticsController;
use App\Http\Controllers\TestController;
use Illuminate\Container\Attributes\Auth;
use Illuminate\Support\Facades\Route;
use League\Csv\Query\Row;

Route::get('/', [GeneralPageController::class, 'showGeneralPage'])->name('home'); // Главная, доступна после авторизации

// Авторизация и регистрация
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Тест
// Route::get('/test', [TestController::class, 'colortest'])->name('test')->middleware('auth');
// Route::get('/tests', [TestController::class, 'index'])->name('tests')->middleware('auth');
// Route::post('/save-result', [TestController::class, 'store'])->name('save.result')->middleware('auth');

// Профиль
Route::get('/profile', [ProfileController::class, 'index'])->name('profile')->middleware('auth');
Route::patch('/profile/update', [AuthController::class, 'update'])->name('profile.update')->middleware('auth');
Route::get('/result/{id}', [ProfileController::class, 'showResult'])->name('result')->middleware('auth');
// Route::get('/packages/{package}/purchase', [PackageController::class, 'purchase'])->name('packages.purchase')->middleware('auth');
// Route::get('/payment/{purchase}', [PackageController::class, 'payment'])->name('packages.payment')->middleware('auth');
// Route::post('/packages/{package}/store', [PackageController::class, 'store'])->name('packages.store')->middleware('auth');
Route::get('/packages/{package}/payment', [PackageController::class, 'payment'])->name('packages.payment')->middleware('auth');
Route::post('/packages/{package}/store', [PackageController::class, 'store'])->name('packages.store')->middleware('auth');
Route::get('/profile/download-pdf', [ProfileController::class, 'downloadPdf'])->name('profile.download-pdf')->middleware('auth');
Route::get('/profile/download-doc', [ProfileController::class, 'downloadDoc'])->name('profile.download-doc')->middleware('auth');
Route::get('/profile/report', [ProfileController::class, 'showReport'])->name('profile.report')->middleware('auth');

Route::prefix('/tests')->middleware('auth')->name('tests.')->group(function () {
    Route::get('/', [TestController::class, 'index'])->name('index');
    Route::get('/{test}', [TestController::class, 'show'])->name('show');
    Route::post('/{test}/save', [TestController::class, 'save'])->name('save');
});
// Администрирование
Route::middleware(['auth', 'admin'])->prefix('/admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('index');
    // Route::get('/users', [AdminController::class, 'indexUsers'])->name('users.index');
    // Route::get('/users/{user}/edit', [AdminController::class, 'editUser'])->name('users.edit');
    // Route::put('/users/{user}', [AdminController::class, 'updateUser'])->name('users.update');
    // Route::delete('/users/{user}', [AdminController::class, 'destroyUser'])->name('users.destroy');


    Route::get('/results', [AdminController::class, 'indexResults'])->name('results.index');
    // Route::get('/results/{result}/edit', [AdminController::class, 'editResult'])->name('results.edit');
    // Route::put('/results/{result}', [AdminController::class, 'updateResult'])->name('results.update');
    // Route::delete('/results/{result}', [AdminController::class, 'destroyResult'])->name('results.destroy');

    Route::get('/purchases', [AdminController::class, 'purchases'])->name('purchases.index');
    Route::post('/purchases/{purchase}/verify', [AdminController::class, 'verifyPurshase'])->name('purchases.verify');

    Route::get('/admin/results/download-pdf/{userId}', [AdminController::class, 'downloadUserPdf'])->name('results.download-pdf')->middleware('auth');
    Route::get('/admin/results/download-doc/{userId}', [AdminController::class, 'downloadUserDoc'])->name('results.download-doc')->middleware('auth');
});

