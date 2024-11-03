<?php

namespace App\Http\Controllers;

use App\Models\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\Models\User;
use App\Models\CardPrice;
use App\Models\Card;

class CollectionController extends Controller
{
    // Fetch user collection by auth token
    public function getUserCollection(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'User not found.'], 404);
        }

        $collection = Collection::where('user_id', $user->id)->with('card')->get();
        return response()->json($collection);
    }

    // Add card to user's collection
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
                'card_id' => $validated['card_id'], // Use card_id directly
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


    $collection = Collection::where([
        'user_id' => $user->id,
        'card_id' => $validated['card_id'],
    ])->first();

    if (!$collection) {
        return response()->json(['message' => 'Card not found in collection.'], 404);
    }


   
        //update with reduced / updated count
        $collection = Collection::updateOrCreate(
            [
                'user_id' => $user->id,
                'card_id' => $validated['card_id'],
            ]);
        
    $variantColumn = $validated['variant'] === 'holofoil' ? 'holo_count' : ($validated['variant'] === 'reverseHolofoil' ? 'reverse_holo_count' : ($validated['variant']=='normal' ?  'normal_count' : ''));
    $collection->$variantColumn -= 1;

    // Save the updated collection
    $collection->save();
    


        return response()->json([
            'message' => 'Card count updated in collection.',
           
        ], 200);
  
}


}