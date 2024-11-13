<?php

namespace App\Http\Controllers;

use App\Models\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\Traits\CardPriceMergeTrait;
// use App\Models\User;
// use App\Models\CardPrice;
// use App\Models\Card;

class CollectionController extends Controller
{
    
    public function getUserCollection(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'User  not found.'], 404);
        }
    
    
     $collection = Collection::where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
    ->with(['card.set']) 
    ->get();
      
        $cards = $collection->pluck('card')->filter();
        $cardPrices = DB::table('cardprices')
            ->whereIn('id', $cards->pluck('cardprice_id'))
            ->select('id', 'tcgplayer', 'cardmarket') 
            ->get()
            ->keyBy('id');

            $totalValue = 0;

       //merge cards with according price data 
        $cards = $cards->map(function ($card) use ($cardPrices) {
            if (isset($cardPrices[$card->cardprice_id])) {
                $priceData = $cardPrices[$card->cardprice_id];
                
                $decodedTcgplayerPrices = json_decode($priceData->tcgplayer, true);
                $decodedCardmarketPrices = json_decode($priceData->cardmarket, true);
    
                //merging into cardobject 
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
    
        
      
    $collection->transform(function ($item) use ($cards, &$totalValue) {
        $item->card = $cards->firstWhere('card_id', $item->card_id);
        
        if ($item->card && isset($item->card->price_data)) {
            $priceData = $item->card->price_data;
            
           //value for each variant
            $normalValue = ($item->normal_count * ($priceData['tcgplayer']['normal']['market'] ?? 0));
            $holoValue = ($item->holo_count * ($priceData['tcgplayer']['holofoil']['market'] ?? 0));
            $reverseHoloValue = ($item->reverse_holo_count * ($priceData['tcgplayer']['reverseHolofoil']['market'] ?? 0));
            
            $itemTotalValue = $normalValue + $holoValue + $reverseHoloValue;
            
            $item->total_value = $itemTotalValue;
            $totalValue += $itemTotalValue;
        }
        
        return $item;
    });
    
    $result = [
       $collection,
        'total_collection_value' => $totalValue
    ];

    return response()->json($result);
}
  
    //add card to user collection
    public function addCardToCollection(Request $request)
    {
        $validated = $request->validate([
              
        'card_id' => 'required|exists:cards,card_id',
        'variant' => 'required|in:normal,holofoil,reverseHolofoil',
        'count' => 'required|integer|min:1',
        ]);

         $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'User not found.'], 404);
        }

        $card = DB::table('cards')
        ->where('card_id', $validated['card_id'])
        ->first();

        if (!$card) {
            return response()->json(['message' => 'Card not found.'], 404);
        }

        $cardPrice = DB::table('cardprices')
            ->where('id', $card->cardprice_id) 
            ->first();

        if (!$cardPrice) {
            return response()->json(['message' => 'Card price data not found.'], 404);
        }

        $decodedTcgplayerPrices = json_decode($cardPrice->tcgplayer, true);
        // $decodedCardmarketPrices = json_decode($cardPrice->cardmarket, true);  <--  commented out for now, doesnt let user store a reverseholofoil
        //we'll see if needed in future 
        
        $priceData = [
            'tcgplayer' => $decodedTcgplayerPrices['prices'] ?? [],
            // 'cardmarket' => $decodedCardmarketPrices['prices'] ?? [],
        ];

            $collection = Collection::firstOrNew([

                'user_id' => $user->id,
                'card_id' => $validated['card_id'], 
            ]);

                // $variantColumn = $validated['variant'] === 'holofoil' ? 'holo_count' : ($validated['variant'] === 'reverseHolofoil' ? 'reverse_holo_count' : ($validated['variant']=='normal' ?  'normal_count' : ''));
                // $collection->$variantColumn += $validated['count'];
                $variantColumn = $validated['variant'] === 'holofoil' ? 'holo_count' : ($validated['variant'] === 'reverseHolofoil' ? 'reverse_holo_count' : ($validated['variant']=='normal' ?  'normal_count' : ''));
                $collection->$variantColumn += 1;
                $collection->save();

                    $collection->price_data = $priceData;

    return response()->json($collection, 201);
    }
        
    
   //remove card from user collection 
    public function removeCardFromCollection(Request $request)
    {
        $validated = $request->validate([
        
            'card_id' => 'required|exists:cards,card_id',
            'variant' => 'required|in:normal,holofoil,reverseHolofoil',
            'count' => 'required|integer|min:1',
        ]);
    
    
        $user = Auth::user();

                if (!$user) {
                    return response()->json(['message' => 'User not found.'], 404);
                }
    
        $card = DB::table('cards')
        ->where('card_id', $validated['card_id'])
        ->first();

        if (!$card) {
            return response()->json(['message' => 'Card not found.'], 404);
        }

        $cardPrice = DB::table('cardprices')
            ->where('id', $card->cardprice_id) 
            ->first();

        if (!$cardPrice) {
            return response()->json(['message' => 'Card price data not found.'], 404);
        }

        //collection entry
        $collection = Collection::where([
            'user_id' => $user->id,
            'card_id' => $validated['card_id'],
        ])->first();
            
        $variantColumn = $validated['variant'] === 'holofoil' ? 'holo_count' : ($validated['variant'] === 'reverseHolofoil' ? 'reverse_holo_count' : 'normal_count');

        //decrease for speicicr variant 
        $collection->$variantColumn -= 1;
    
      //checks if all variant counts are zero
      //if so, delete the collection entry
        if ($collection->normal_count <= 0 && $collection->holo_count <= 0 && $collection->reverse_holo_count <= 0) {
            $collection->delete();
            return response()->json(['message' => 'Card removed from collection.'], 200);

        } else {
            $collection->save();
            return response()->json(['message' => 'Card count updated in collection.'], 200);
        }
        
    }
    
    //function to sort most recent added cards on top
    public function sortCollection()
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'User not found.'], 404);

            }

        $collection = Collection::where('user_id', $user->id)
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json($collection);
        }

}