<?php

namespace App\Http\Controllers;
use App\Models\Card;
use App\Models\Set;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CardController extends Controller
{
    //all cards
    public function index() {
        $cards = Card::all();
        return view('cards.index', compact('cards'));
    }

    //cards from set
    public function cardsFromSet($setId, Request $request){

   
        $cards = Set::with('cards.set')->find($setId)->cards;
       

        
    return response()->json($cards);
    }


    //searching for a card, even by id
    public function search(Request $request)
    {
        $query = $request->input('query');
    
        //check if includes both card_id and printed_total -> identifier of card 
        if (preg_match('/(\d+)\/(\d+)/', $query, $matches)) {
            $cardIdPart = $matches[1]; 
            $printedTotal = $matches[2]; 
    
            
            $results = Card::whereRaw('CAST(SUBSTRING_INDEX(card_id, "-", -1) AS UNSIGNED) = ?', [$cardIdPart])
                ->whereHas('set', function ($query) use ($printedTotal) {
                    $query->where('printed_total', $printedTotal);
                })
                ->get();
        } else {
            $results = Card::where('name', 'LIKE', '%' . $query . '%')->get();
        }
    
        if ($results->isEmpty()) {
            return response()->json(['message' => 'No card found'], 404);
        }
    
        return response()->json($results);
    }
    

    //filter by set
    public function filterType(Request $request)
    {
        $type = $request->input('type');
        
        if (!$type) {
            return response()->json(['error' => 'Type is required'], 400);
        }
        
        $query = Card::query();
    
        //filter by set if set_id is present
        if ($request->has('set_id')) {
            $setId = $request->input('set_id');
            $query->where('set_id', $setId);
        }
    
        //filter by type
        $cards = $query->where('types', 'LIKE', '%' . $type . '%')->get();
        
        // Return filtered cards
        return response()->json($cards);
    }
    
    public function orderEvolutionBySets($setId) {
       
        //fetch cards from current
        $set = Set::with('cards')->find($setId);

        if (!$set) {
            Log::info("No set found for ID: $setId");
            return response()->json([]); 
        }
        
        $cards = $set->cards;

        if ($cards->isEmpty()) {
            Log::info("No cards found for set ID: $setId");
            return response()->json([]); 
        }
    
        //map of cards
        $cardMap = [];
        foreach ($cards as $card) {
            $cardMap[$card->name] = $card;
        }
    
        //grouping cards by evo chain
        $evolutionChains = [];
    
        foreach ($cards as $card) {
            //(Basic, Stage 1, Stage 2)
            $evolutionStage = 'Basic'; 
            if (in_array('Stage 2', $card->subtypes)) {
                $evolutionStage = 'Stage 2';
            } elseif (in_array('Stage 1', $card->subtypes)) {
                $evolutionStage = 'Stage 1';
            }
            //starting an evo chain from Basic
            if ($evolutionStage === 'Basic' || empty($card->evolves_from)) {
                $evolutionChains[$card->name] = [$card];
            } else {
                //if stage 1 or stage 2, add to the corresponding chain based on 'evolves_from'
                if (isset($evolutionChains[$card->evolves_from])) {
                   
                    $evolutionChains[$card->evolves_from][] = $card;

                } elseif (isset($cardMap[$card->evolves_from])) {
                  
                    $evolutionChains[$card->evolves_from] = [$cardMap[$card->evolves_from], $card];
                }
            }
        }
    
        //sorted list
        $sortedEvo = [];
        foreach ($evolutionChains as $chain) {
            //sorting by from basic -> s1 -> s2
            usort($chain, function($a, $b) {
                return $this->getEvolutionOrder($a) - $this->getEvolutionOrder($b);
            });
    
            // Add the sorted chain to the final result
            foreach ($chain as $card) {
                $sortedEvo[] = $card;
            }
        }
    
        //cards that do not have a basic as previous, so cards as EX and such
        foreach ($cards as $card) {
            if (empty($card->evolves_from) && !array_key_exists($card->name, $evolutionChains)) {
                $sortedEvo[] = $card; 
            }
        }
    
        return response()->json($sortedEvo);
    }
    
    private function getEvolutionOrder($card) {
        if (in_array('Stage 2', $card->subtypes)) {
            return 3; 
        } elseif (in_array('Stage 1', $card->subtypes)) {
            return 2; 
        }
        return 1;
    }
    
    public function subTypes($setId)
{
    $set = Set::with('cards')->find($setId);

    if (!$set) {
        return response()->json(['message' => 'Set not found'], 404);
    }

    $cards = $set->cards;

    if ($cards->isEmpty()) {
        return response()->json(['message' => 'No cards found in this set'], 404);
    }

    //subtypes to exclude (evolutions)
    $excludedSubtypes = [
        "Basic", "Stage 1", "Stage 2", "Stage 3",
    ];

    $subtypeArray = [];
    // checking if subtype exists and if is an array
    foreach ($cards as $card) {
        if (isset($card->subtypes) && is_array($card->subtypes)) {
            foreach ($card->subtypes as $subtype) {
                if (!in_array($subtype, $excludedSubtypes) && !in_array($subtype, $subtypeArray)) {
                    $subtypeArray[] = $subtype;
                }
            }
        }
    }

    return response()->json($subtypeArray);
}
}
