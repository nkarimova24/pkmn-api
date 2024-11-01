<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CommentedFunctions extends Controller
{
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
    // public function getCardPrices($cardId) {
    //     $prices = DB::table('cards')
    //         ->join('cardprices', 'cards.card_id', '=', 'cardprices.id')
    //         ->select('cards.card_id', 'cardprices.tcgplayer')
    //         ->where('cards.card_id', $cardId)
    //         ->first();
    
    //     if (!$prices) {
    //         return response()->json(['error' => 'Price not found'], 404);
    //     }
    
    //     // Decode the JSON string in tcgplayer
    //     $decodedPrices = json_decode($prices->tcgplayer, true);
    
    //     // Check if the decoding was successful
    //     if ($decodedPrices === null) {
    //         return response()->json(['error' => 'Failed to decode tcgplayer data'], 500);
    //     }
    
    //     // Create an associative array to return the desired structure
    //     $result = [
    //         'card_id' => $prices->card_id,
    //         'url' => $decodedPrices['url'] ?? null,
    //         'updatedAt' => $decodedPrices['updatedAt'] ?? null,
    //         'prices' => [
    //             'holofoil' => $decodedPrices['prices']['holofoil'] ?? null,
    //         ],
    //     ];
    
    //     return response()->json($result);
    // }
    
    
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
}
