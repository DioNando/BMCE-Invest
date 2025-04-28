<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// /*
// |--------------------------------------------------------------------------
// | Web Routes
// |--------------------------------------------------------------------------
// */

// // Public routes
// Route::get('/', [HomeController::class, 'index'])->name('home');

// // Authentication routes (handled by Laravel Fortify)

// // Protected routes
// Route::middleware(['auth'])->group(function () {
//     // Admin routes
//     Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {
//         Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
//         Route::resources([
//             'meetings' => MeetingController::class,
//             'organizations' => OrganizationController::class,
//             'rooms' => RoomController::class,
//             'users' => UserController::class,
//         ]);
//     });

//     // Investor routes
//     Route::middleware(['role:investor'])->prefix('investor')->name('investor.')->group(function () {
//         Route::get('/dashboard', [InvestorDashboardController::class, 'index'])->name('dashboard');
//         Route::post('/questions', [QuestionController::class, 'store'])->name('questions.store');
//         Route::delete('/questions/{question}', [QuestionController::class, 'destroy'])->name('questions.destroy');
//     });

//     // Issuer routes
//     Route::middleware(['role:issuer'])->prefix('issuer')->name('issuer.')->group(function () {
//         Route::get('/dashboard', [IssuerDashboardController::class, 'index'])->name('dashboard');
//     });

//     // Redirect based on role
//     Route::get('/dashboard', function () {
//         if (auth()->user()->hasRole('admin')) {
//             return redirect()->route('admin.dashboard');
//         } elseif (auth()->user()->hasRole('issuer')) {
//             return redirect()->route('issuer.dashboard');
//         } elseif (auth()->user()->hasRole('investor')) {
//             return redirect()->route('investor.dashboard');
//         }

//         return redirect()->route('home');
//     })->name('dashboard');
// });

require __DIR__ . '/auth.php';
