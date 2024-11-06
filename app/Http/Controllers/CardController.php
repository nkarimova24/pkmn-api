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


    //cards from set
    public function cardsFromSet($setId, Request $request)
    {
        //cards from table cards
        $cards = Set::with('cards.set')
            ->findOrFail($setId)
            ->cards;
    
        if ($cards->isEmpty()) {
            return response()->json(['error' => 'No cards found for this set'], 404);
        }
    
        //pricedata from cardprices
        $cardPrices = DB::table('cardprices')
            ->whereIn('id', $cards->pluck('cardprice_id'))
            ->select('id', 'tcgplayer', 'cardmarket') 
            ->get()
            ->keyBy('id');
    
        //merge pricedata with card standard 
        $cards = $cards->map(function ($card) use ($cardPrices) {
            if (isset($cardPrices[$card->cardprice_id])) {
                $priceData = $cardPrices[$card->cardprice_id];
                
                //decoding price data since it's stored in array
                $decodedTcgplayerPrices = json_decode($priceData->tcgplayer, true);
                $decodedCardmarketPrices = json_decode($priceData->cardmarket, true);
    
                $card->price_data = [
                    'id' => $card->cardprice_id,
                    'tcgplayer' => [
                        'url' => $decodedTcgplayerPrices['url'] ?? null,
                        'updatedAt' => $decodedTcgplayerPrices['updatedAt'] ?? null,
                        'normal' => $decodedTcgplayerPrices['prices']['normal'] ?? null,
                        'reverseHolofoil' => $decodedTcgplayerPrices['prices']['reverseHolofoil'] ?? null,
                        'holofoil' => $decodedTcgplayerPrices['prices']['holofoil'] ?? null,
                    ],
                    'cardmarket' => [
                        'url' => $decodedCardmarketPrices['url'] ?? null,
                        'updatedAt' => $decodedCardmarketPrices['updatedAt'] ?? null,
                        'prices' => [
                            'averageSellPrice' => $decodedCardmarketPrices['prices']['averageSellPrice'] ?? null,
                            'lowPrice' => $decodedCardmarketPrices['prices']['lowPrice'] ?? null,
                            'trendPrice' => $decodedCardmarketPrices['prices']['trendPrice'] ?? null,
                            'germanProLow' => $decodedCardmarketPrices['prices']['germanProLow'] ?? null,
                            'suggestedPrice' => $decodedCardmarketPrices['prices']['suggestedPrice'] ?? null,
                            'reverseHoloSell' => $decodedCardmarketPrices['prices']['reverseHoloSell'] ?? null,
                            'reverseHoloLow' => $decodedCardmarketPrices['prices']['reverseHoloLow'] ?? null,
                            'reverseHoloTrend' => $decodedCardmarketPrices['prices']['reverseHoloTrend'] ?? null,
                            'lowPriceExPlus' => $decodedCardmarketPrices['prices']['lowPriceExPlus'] ?? null,
                            'avg1' => $decodedCardmarketPrices['prices']['avg1'] ?? null,
                            'avg7' => $decodedCardmarketPrices['prices']['avg7'] ?? null,
                            'avg30' => $decodedCardmarketPrices['prices']['avg30'] ?? null,
                            'reverseHoloAvg1' => $decodedCardmarketPrices['prices']['reverseHoloAvg1'] ?? null,
                            'reverseHoloAvg7' => $decodedCardmarketPrices['prices']['reverseHoloAvg7'] ?? null,
                            'reverseHoloAvg30' => $decodedCardmarketPrices['prices']['reverseHoloAvg30'] ?? null,
                        ],
                    ],
                ];
            }
    
            return $card;
        });
    
        return response()->json($cards);
    }
    //searching for a card, even by id
    public function search(Request $request)
    {
        $query = $request->input('query');
    
        // Check if includes both card_id and printed_total -> identifier of card 
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
    
        // Fetch price data for the found cards
        $cardPrices = DB::table('cardprices')
            ->whereIn('id', $results->pluck('cardprice_id'))
            ->select('id', 'tcgplayer', 'cardmarket') 
            ->get()
            ->keyBy('id');
    
        // Merge price data with card data
        $results = $results->map(function ($card) use ($cardPrices) {
            if (isset($cardPrices[$card->cardprice_id])) {
                $priceData = $cardPrices[$card->cardprice_id];
    
                // Decoding price data since it's stored in array
                $decodedTcgplayerPrices = json_decode($priceData->tcgplayer, true);
                $decodedCardmarketPrices = json_decode($priceData->cardmarket, true);
    
                $card->price_data = [
                    'id' => $card->cardprice_id,
                    'tcgplayer' => [
                        'url' => $decodedTcgplayerPrices['url'] ?? null,
                        'updatedAt' => $decodedTcgplayerPrices['updatedAt'] ?? null,
                        'normal' => $decodedTcgplayerPrices['prices']['normal'] ?? null,
                        'reverseHolofoil' => $decodedTcgplayerPrices['prices']['reverseHolofoil'] ?? null,
                        'holofoil' => $decodedTcgplayerPrices['prices']['holofoil'] ?? null,
                    ],
                    'cardmarket' => [
                        'url' => $decodedCardmarketPrices['url'] ?? null,
                        'updatedAt' => $decodedCardmarketPrices['updatedAt'] ?? null,
                        'prices' => [
                            'averageSellPrice' => $decodedCardmarketPrices['prices']['averageSellPrice'] ?? null,
                            'lowPrice' => $decodedCardmarketPrices['prices']['lowPrice'] ?? null,
                            'trendPrice' => $decodedCardmarketPrices['prices']['trendPrice'] ?? null,
                            'germanProLow' => $decodedCardmarketPrices['prices']['germanProLow'] ?? null,
                            'suggestedPrice' => $decodedCardmarketPrices['prices']['suggestedPrice'] ?? null,
                            'reverseHoloSell' => $decodedCardmarketPrices['prices']['reverseHoloSell'] ?? null,
                            'reverseHoloLow' => $decodedCardmarketPrices['prices']['reverseHoloLow'] ?? null,
                            'reverseHoloTrend' => $decodedCardmarketPrices['prices']['reverseHoloTrend'] ?? null,
                            'lowPriceExPlus' => $decodedCardmarketPrices['prices']['lowPriceExPlus'] ?? null,
                            'avg1' => $decodedCardmarketPrices['prices']['avg1'] ?? null,
                            'avg7' => $decodedCardmarketPrices['prices']['avg7'] ?? null,
                            'avg30' => $decodedCardmarketPrices['prices']['avg30'] ?? null,
                            'reverseHoloAvg1' => $decodedCardmarketPrices['prices']['reverseHoloAvg1'] ?? null,
                            'reverseHoloAvg7' => $decodedCardmarketPrices['prices']['reverseHoloAvg7'] ?? null,
                            'reverseHoloAvg30' => $decodedCardmarketPrices['prices']['reverseHoloAvg30'] ?? null,
                        ],
                    ],
                ];
            }
    
            return $card;
        });
    
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
