<?php
use Illuminate\Support\Facades\Route; 
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Web\ProfileController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Web\ActivityLogController;
use App\Http\Controllers\Web\PostController as WebPostController;
use App\Http\Controllers\Web\PlatformController as WebPlatformController;
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

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login'); 
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout'); 

// If you want registration for web:
// Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
// Route::post('/register', [RegisterController::class, 'register']);


Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/dashboard', [WebPostController::class, 'dashboard'])->name('dashboard');
    Route::get('/posts/create', [WebPostController::class, 'create'])->name('posts.create');
    Route::post('/posts', [WebPostController::class, 'store'])->name('posts.store'); 

    Route::get('/posts/{post}/edit', [WebPostController::class, 'edit'])->name('posts.edit');
    Route::put('/posts/{post}', [WebPostController::class, 'update'])->name('posts.update'); 
    Route::delete('/posts/{post}', [WebPostController::class, 'destroy'])->name('posts.destroy');
    Route::get('/settings/platforms', [WebPlatformController::class, 'index'])->name('settings.platforms');
    Route::get('/activity-log', [ActivityLogController::class, 'index'])->name('activity.log');
     Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/password', [ProfileController::class, 'updatePassword'])->name('password.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
require __DIR__.'/auth.php';
