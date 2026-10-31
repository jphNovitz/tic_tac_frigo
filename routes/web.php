<?php

use App\Http\Controllers\Admin\DashbordController;
use App\Http\Controllers\Admin\Fridge\FridgeItemController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use Illuminate\Support\Facades\Route;

// Invités (non authentifiés)
Route::middleware('guest')->group(function () {
    Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', [LoginController::class, 'login']);
    Route::get('register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('register', [RegisterController::class, 'register']);
});

// Authentifiés
Route::middleware('auth')->group(function () {
    Route::post('logout', [LoginController::class, 'logout'])->name('logout');
    Route::get('dashboard', [DashbordController::class, 'index'])->name('dashboard');
    Route::get('liste', [FridgeItemController::class, 'index'])->name('fridge-items.index');
    Route::get('nouveau-produit', [FridgeItemController::class, 'create'])->name('fridge-items.create');
    Route::post('nouveau-produit', [FridgeItemController::class, 'store'])->name('fridge-items.store');
});
