<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\LoanController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // User routes
    Route::apiResource('users', UserController::class);

    // Book routes
    Route::apiResource('books', BookController::class);
    Route::get('books/search', [BookController::class, 'search'])->name('books.search');
    Route::get('books/by-genre/{genre}', [BookController::class, 'getByGenre'])->name('books.by-genre');

    // Loan routes
    Route::apiResource('loans', LoanController::class);
    Route::post('loans/{id}/return', [LoanController::class, 'returnBook'])->name('loans.return');
    Route::post('loans/{id}/extend', [LoanController::class, 'extendLoan'])->name('loans.extend');
    Route::get('loans/overdue', [LoanController::class, 'getOverdueLoans'])->name('loans.overdue');
});
