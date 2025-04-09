<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\LoanController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // User routes
    Route::apiResource('users', UserController::class);
    Route::get('users/{id}/loans', [UserController::class, 'loans'])->name('users.loans');

    // Book routes
    Route::apiResource('books', BookController::class);

    // Loan routes
    Route::apiResource('loans', LoanController::class)->except([
        'update', 'destroy'
    ]);
    Route::get('loans/{id}/return', [LoanController::class, 'returnBook'])->name('loans.return');
    Route::get('/loans/{id}/delay', [LoanController::class, 'markAsDelayed'])->name('loans.delay');
});
