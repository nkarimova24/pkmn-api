<?php

namespace App\Http\Controllers;
use App\Models\Card;
use App\Models\Set;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\CardPrice;
use Illuminate\Support\facades\DB;
class CardController extends Controller
{
    //all cards
    public function index() {
        $cards = Card::all();
        return view('cards.index', compact('cards'));
    }

    // public function getCardPrices()
    // {
    //     $cardPrices = DB::table('cardprices as cp')
    //         ->select(
    //             'cp.id as card_id',
    //             DB::raw("cp.tcgplayer->>'$.url' as url"),
    //             DB::raw("cp.tcgplayer->>'$.updatedAt' as updatedAt"),
    //             DB::raw("cp.tcgplayer->'$.prices.holofoil.low' as low"),
    //             DB::raw("cp.tcgplayer->'$.prices.holofoil.mid' as mid"),
    //             DB::raw("cp.tcgplayer->'$.prices.holofoil.high' as high"),
    //             DB::raw("cp.tcgplayer->'$.prices.holofoil.market' as market"),
    //             DB::raw("cp.tcgplayer->'$.prices.holofoil.directLow' as directLow")
    //         )
    //         ->get();
      
    //     return response()->json($cardPrices);
    // }
    // public function getCardPrices()
    // {
    //     $cardPrices = DB::table('cardprices as cp')
    //         ->select('cp.tcgplayer', 'cp.id')
    //         ->get();
    
    //     // Create an array to hold the extracted objects with IDs
    //     $extractedPrices = [];
    
    //     // Loop through each card price and decode the JSON
    //     foreach ($cardPrices as $cardPrice) {
    //         $decodedPrice = json_decode($cardPrice->tcgplayer, true); // Decode the JSON into an associative array
            
    //         // Create an associative array with id and decoded price
    //         $extractedPrices[] = [
    //             'id' => $cardPrice->id,
    //             'price' => $decodedPrice
    //         ];
    //     }
    
    //     return response()->json($extractedPrices);
    // }
    public function getCardPrices($cardId) {
        $prices = DB::table('cards')
            ->join('cardprices', 'cards.card_id', '=', 'cardprices.id')
            ->select('cards.card_id', 'cardprices.tcgplayer')
            ->where('cards.card_id', $cardId)
            ->first();
    
        if (!$prices) {
            return null; // Change this line to return null when no prices found
        }
    
        // Decode the JSON string in tcgplayer
        $decodedPrices = json_decode($prices->tcgplayer, true);
    
        // Check if the decoding was successful
        if ($decodedPrices === null) {
            return response()->json(['error' => 'Failed to decode tcgplayer data'], 500);
        }
    
        // Create an associative array to return the desired structure
        $result = [
            'card_id' => $prices->card_id,
            'url' => $decodedPrices['url'] ?? null,
            'updatedAt' => $decodedPrices['updatedAt'] ?? null,
            'prices' => [
                'holofoil' => $decodedPrices['prices']['holofoil'] ?? null,
                // Provide defaults if they don't exist
                'low' => $decodedPrices['prices']['holofoil']['low'] ?? null,
                'mid' => $decodedPrices['prices']['holofoil']['mid'] ?? null,
                'high' => $decodedPrices['prices']['holofoil']['high'] ?? null,
                'market' => $decodedPrices['prices']['holofoil']['market'] ?? null,
                'directLow' => $decodedPrices['prices']['holofoil']['directLow'] ?? null,
            ],
        ];
    
        return response()->json($result);
    }
    
    
    // public function getCardPrices($cardId)
    // {
    //     // Fetch the card price based on the provided card ID
    //     $cardPrice = DB::table('cardprices as cp')
    //         ->select('cp.tcgplayer', 'cp.id')
    //         ->where('cp.id', $cardId) // Filter by card ID
    //         ->first(); // Get the first result
    
    //     // Check if the card price exists
    //     if (!$cardPrice) {
    //         return response()->json(['message' => 'Card price not found'], 404);
    //     }
    
    //     // Log the raw JSON string
    //     Log::info('Raw JSON for card ID ' . $cardPrice->id . ': ' . $cardPrice->tcgplayer);
    
    //     // Decode the JSON directly
    //     $decodedPrice = json_decode($cardPrice->tcgplayer, true); // Decode the JSON into an associative array
    
    //     // Check if the decoded price is valid
    //     if ($decodedPrice) {
    //         // Create an associative array with id and extracted properties
    //         $extractedPrice = [
    //             'id' => $cardPrice->id,
    //             'url' => $decodedPrice['url'] ?? null,
    //             'updatedAt' => $decodedPrice['updatedAt'] ?? null,
    //             'normal' => $decodedPrice['prices']['normal'] ?? null,
    //             'reverseHolofoil' => $decodedPrice['prices']['reverseHolofoil'] ?? null,
    //             'holofoil' => $decodedPrice['prices']['holofoil'] ?? null, // Added for holofoil prices
    //         ];
    
    //         return response()->json($extractedPrice);
    //     } else {
    //         Log::error('Failed to decode JSON for card ID ' . $cardPrice->id);
    //         return response()->json(['message' => 'Failed to decode card price data'], 500);
    //     }
    // }
//     public function getCardPrices()
// {
//     $cardPrices = DB::table('cardprices as cp')
//         ->select('cp.tcgplayer', 'cp.id')
//         ->get();

//     $extractedPrices = [];

//     foreach ($cardPrices as $cardPrice) {
//         // Decode the JSON string into an associative array
//         $decodedPrice = json_decode($cardPrice->tcgplayer, true);
        
//         // Check if prices and reverseHolofoil exist in the decoded data
//         if (isset($decodedPrice['prices']['reverseHolofoil'])) {
//             $extractedPrices[] = [
//                 'id' => $cardPrice->id,
//                 'price' => [
//                     'updatedAt' => $decodedPrice['updatedAt'],
//                     'reverseHolofoil' => [
//                         'market' => $decodedPrice['prices']['reverseHolofoil']['market'],
//                         'low' => $decodedPrice['prices']['reverseHolofoil']['low'],
//                         'mid' => $decodedPrice['prices']['reverseHolofoil']['mid'],
//                         'high' => $decodedPrice['prices']['reverseHolofoil']['high'],
//                         'directLow' => $decodedPrice['prices']['reverseHolofoil']['directLow']
//                     ]
//                 ]
//             ];
//         }
//     }

//     return response()->json($extractedPrices);
// }
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
