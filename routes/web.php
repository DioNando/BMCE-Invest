<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Investor\DashboardController as InvestorDashboardController;
use App\Http\Controllers\Issuer\DashboardController as IssuerDashboardController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Redirection du dashboard en fonction du rôle utilisateur
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        if (auth()->user()->hasRole('admin')) {
            return redirect()->route('admin.dashboard');
        } elseif (auth()->user()->hasRole('issuer')) {
            return redirect()->route('issuer.dashboard');
        } elseif (auth()->user()->hasRole('investor')) {
            return redirect()->route('investor.dashboard');
        }

        return redirect()->route('home');
    })->name('dashboard');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Routes pour le Dashboard admin
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    // Autres routes d'administration
});

// Routes pour le Dashboard investisseur
Route::middleware(['auth', 'role:investor'])->prefix('investor')->name('investor.')->group(function () {
    Route::get('/dashboard', [InvestorDashboardController::class, 'index'])->name('dashboard');
    // Autres routes d'investisseur
});

// Routes pour le Dashboard émetteur
Route::middleware(['auth', 'role:issuer'])->prefix('issuer')->name('issuer.')->group(function () {
    Route::get('/dashboard', [IssuerDashboardController::class, 'index'])->name('dashboard');
    // Autres routes d'émetteur
});

require __DIR__ . '/auth.php';
