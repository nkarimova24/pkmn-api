<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CardController;
use App\Http\Controllers\SeriesController;
ini_set('memory_limit', '-1');




//series and their sets in one :)
Route::get('/series', [SeriesController::class, 'index']  );
   

//card to according set
Route::get('/sets/{setId}/cards', [CardController::class, 'cardsFromSet']);


