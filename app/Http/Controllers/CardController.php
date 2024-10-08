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


    //searching for a card
    public function search(Request $request)
    {
       
        $query = $request->input('query');
        
        if (!$query) {
            return response()->json(['error' => 'Search query is required'], 400);
        }

        $results = Card::where('name', 'LIKE', '%' . $query . '%')->get();

        if ($results->isEmpty()) {
            return response()->json(['message' => 'No card found'], 404);
        }

        return response()->json($results);
    }

}
