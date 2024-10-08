<?php

namespace App\Http\Controllers;
use App\Models\Card;
use App\Models\Set;
use Illuminate\Http\Request;

class CardController extends Controller
{
    //all cards
    public function index() {
        $cards = Card::all();
        return view('cards.index', compact('cards'));
    }

    //cards from set
    public function cardsFromSet($setId){
        $cards = Set::find($setId)->cards;
        return response()->json($cards);
    }
}
