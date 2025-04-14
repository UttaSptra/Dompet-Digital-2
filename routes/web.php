<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AuthController;

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

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth', 'is_admin'])->group(function () {
    Route::get('/admin', [HomeController::class, 'adminindex'])->name('admin.index');
    Route::post('/admin', [HomeController::class, 'adminstore'])->name('admin.store');
    Route::get('/admin/edit/{id}', [HomeController::class, 'adminedit'])->name('admin.edit');
    Route::put('/admin', [HomeController::class, 'adminupdate'])->name('admin.update');
    Route::delete('/admin/delete/{id}', [HomeController::class, 'destroy'])->name('admin.delete');
});

Route::middleware(['auth', 'is_bank'])->group(function () {
    Route::get('/bank', [HomeController::class, 'bankindex'])->name('bank.index');
    Route::get('/bank/topups', [HomeController::class, 'bankindex'])->name('bank.topups');
    Route::post('/bank/topups/{id}/approve', [HomeController::class, 'bankapprove'])->name('bank.topups.approve');
    Route::post('/bank/topups/{id}/reject', [HomeController::class, 'bankreject'])->name('bank.topups.reject');
    Route::post('/bank/cash-deposit/{userId}', [HomeController::class, 'bankcashdeposit'])->name('bank.cash.deposit');
});

Route::get('/dashboard', [HomeController::class, 'dashboard'])->middleware('auth')->name('dashboard');