<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CardController;
use App\Http\Controllers\SeriesController;
use App\Http\Controllers\CollectionController;
use App\Http\Controllers\API\AuthController;


ini_set('memory_limit', '-1');


//registration 
// Route::post('/register', [RegistrationController::class, 'create']);
Route::post('/register', [App\Http\Controllers\API\AuthController::class, 'register'])->name('register');

//login
Route::post('login', [AuthController::class, 'login']);

