<?php

namespace App\Http\Controllers;

use App\Models\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\User;
use App\Models\CardPrice;
use App\Models\Card;

class CollectionController extends Controller
{

    //fetch user collection by email
    public function getUserCollection(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|exists:users,email', 
        ]);

        $user = DB::table('users')->where('email', $validated['email'])->first();

        if (!$user) {
            return response()->json(['message' => 'User not found.'], 404);
        }

        $collection = Collection::where('user_id', $user->id)->with('card')->get(); 
        return response()->json($collection);
    }

    //add card to users collection
    public function addCardToCollection(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|exists:users,email',
            'card_id' => 'required|exists:cards,card_id',
            'count' => 'required|integer|min:1',
            'variant' => 'required|in:normal,holofoil,reverseHolofoil',
        ]);
    
        $user = User::where('email', $validated['email'])->firstOrFail();

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


        $variationKey = $validated['variant'] === 'reverseHolofoil' ? 'reverseHolofoil' : $validated['variant'];

        if (!isset($priceData['tcgplayer'][$variationKey])) { 
            return response()->json(['message' => 'Variation prices not found for ' . $validated['variant']], 404);
        }

            $collection = Collection::firstOrNew([
                'user_id' => $user->id,
                'card_id' => $validated['card_id'], // Use card_id directly
            ]);

                $variantColumn = $validated['variant'] === 'holofoil' ? 'holo_count' : ($validated['variant'] === 'reverseHolofoil' ? 'reverse_holo_count' : 'normal_count');
                $collection->$variantColumn += $validated['count'];

                $collection->save();

                    $collection->price_data = $priceData;

    return response()->json($collection, 201);
    }
        
    
    
    //remove card from user collection 
    public function removeCardFromCollection(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|exists:users,email',
            'card_id' => 'required|exists:cards,card_id',
            'count' => 'required|integer|min:1',
        ]);
    
        $user = DB::table('users')->where('email', $validated['email'])->first();
    
        if (!$user) {
            return response()->json(['message' => 'User not found.'], 404);
        }
    
        //collection entry
        $collection = Collection::where([
            'user_id' => $user->id,
            'card_id' => $validated['card_id'],
        ])->first();
    
        if (!$collection) {
            return response()->json(['message' => 'Card not found in collection.'], 404);
        }
    
       //new count
        $newCount = $collection->count - $validated['count'];
    
        if ($newCount <= 0) {
            $collection->delete();
            return response()->json([
                'message' => 'Card removed from collection completely.',
                'count' => 0
            ], 200);
        } else {
            //update with reduced / updated count
            $collection = Collection::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'card_id' => $validated['card_id'],
                ],
                ['count' => $newCount]
            );
    
            return response()->json([
                'message' => 'Card count updated in collection.',
                'count' => $newCount
            ], 200);
        }
    }

}