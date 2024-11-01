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
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
// //logout
Route::get('/user', [AuthController::class, 'user'])->middleware('auth'); 
// Route::post('/logout', [LoginController::class, 'logout']);

//series and their sets in one :)
Route::get('/series', [SeriesController::class, 'index']  );

//card to according set
Route::get('/sets/{setId}/cards', [CardController::class, 'cardsFromSet']);
Route::get('/card-prices/{cardId}', [CardController::class, 'getCardPrices']);
//searching for specific card
Route::get('/search', [CardController::class, 'search']);

//types && subtypes
Route::get('/cards/filter', [CardController::class, 'filterType']);
//
Route::get('/subtypes/{setId}', [CardController::class, 'subTypes']);

//evolutionchains per set
Route::get('/cards/set/{setId}/sorted', [CardController::class, 'orderEvolutionBySets']);

Route::post('/collections/add', [CollectionController::class, 'addCardToCollection'])->name('addCardToCollection');
Route::get('/collections', [CollectionController::class, 'getUserCollection']); // Accepts email in the request
Route::delete('/collections/remove', [CollectionController::class, 'removeCardFromCollection']);