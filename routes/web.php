<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\CommentToReviewController;
use App\Http\Controllers\GeneralPageController;
use App\Http\Controllers\PHPMailerController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReviewController;
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
Route::post('/profile/update', [ProfileController::class, 'update'])->middleware('auth');
Route::post('/profile/message', [ProfileController::class, 'sendMessage'])->name('profile.sendMessage')->middleware('auth');
Route::get('/results', [ProfileController::class, 'showResults'])->name('results')->middleware('auth');
Route::get('/result/{id}', [ProfileController::class, 'showResult'])->name('result')->middleware('auth');
Route::get('/results/search', [ProfileController::class, 'search'])->name('results.search')->middleware('auth');

// Отзывы
Route::get('/reviews', [ReviewController::class, 'index'])->name('reviews')->middleware('auth');
Route::get('/reviews/create', [ReviewController::class, 'create'])->name('reviews.create')->middleware('auth');
Route::post('/reviews', [ReviewController::class, 'store'])->name('reviews.store')->middleware('auth');
Route::get('/reviews/search', [ReviewController::class, 'search'])->name('reviews.search');
Route::post('/comments/{reviewId}', [CommentToReviewController::class, 'store'])->name('comments.store');
// Администрирование 
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin', [AdminController::class, 'index'])->name('admin.index');
    Route::get('/admin/users', [AdminController::class, 'indexUsers'])->name('admin.users.index');
    Route::get('/admin/users/{user}/edit', [AdminController::class, 'editUser'])->name('admin.users.edit');
    Route::put('/admin/users/{user}', [AdminController::class, 'updateUser'])->name('admin.users.update');
    Route::delete('/admin/users/{user}', [AdminController::class, 'destroyUser'])->name('admin.users.destroy');
    Route::get('/admin/users/{user}/message', [AdminController::class, 'showMessageForm'])->name('admin.users.message');
    Route::post('/admin/users/{user}/message', [AdminController::class, 'sendMessage'])->name('admin.users.message');
    Route::post('/admin/users/message', [AdminController::class, 'replyMessage'])->name('admin.users.reply');

    Route::get('/admin/reviews', [AdminController::class, 'indexReviews'])->name('admin.reviews.index');
    Route::get('/admin/reviews/{review}/edit', [AdminController::class, 'editReview'])->name('admin.reviews.edit');
    Route::put('/admin/reviews/{review}', [AdminController::class, 'updateReview'])->name('admin.reviews.update');
    Route::delete('/admin/reviews/{review}', [AdminController::class, 'destroyReview'])->name('admin.reviews.destroy');

    Route::get('/admin/results', [AdminController::class, 'indexResults'])->name('admin.results.index');
    Route::get('/admin/results/{result}/edit', [AdminController::class, 'editResult'])->name('admin.results.edit');
    Route::put('/admin/results/{result}', [AdminController::class, 'updateResult'])->name('admin.results.update');
    Route::delete('/admin/results/{result}', [AdminController::class, 'destroyResult'])->name('admin.results.destroy');
});
