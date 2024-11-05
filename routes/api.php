<?php

use App\Http\Controllers\API\Auth\LoginController;
use App\Http\Controllers\API\Auth\RegisterController;
use App\Http\Controllers\API\LoanController;
use Illuminate\Support\Facades\Route;

/**
 * User Authentication Routes
 * 
 * Routes for user registration and login. These routes are public and do not require authentication.
 */
Route::controller(RegisterController::class)->group(function() {
    // Register a new user
    Route::post('register', 'register');
});

Route::controller(LoginController::class)->group(function() {
    // Login an existing user
    Route::post('login', 'login');
});

/**
 * Loan API Routes
 * 
 * Public Routes - No authentication required for reading loans.
 * These routes allow any user to list all loans or view a specific loan by ID.
 */
Route::get('loans', [LoanController::class, 'index']); // List all loans (public access)
Route::get('loans/{id}', [LoanController::class, 'show']); // Show a specific loan by ID (public access)

/**
 * Protected Loan API Routes (Requires Authentication)
 * 
 * Only authenticated users can create, update, or delete loans. 
 * These routes are protected by auth:sanctum middleware.
 */
Route::middleware('auth:sanctum')->prefix('loans')->group(function() {
    // Create a new loan (only accessible to authenticated users, typically lenders)
    Route::post('/', [LoanController::class, 'store']);
    
    // Update an existing loan by ID (only the original lender can update)
    Route::patch('/{id}', [LoanController::class, 'update']);
    
    // Delete an existing loan by ID (only the original lender can delete)
    Route::delete('/{id}', [LoanController::class, 'destroy']);
});
