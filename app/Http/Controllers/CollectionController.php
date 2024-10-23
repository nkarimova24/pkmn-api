<?php

namespace App\Http\Controllers;

use App\Models\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CollectionController extends Controller
{
    // Add card to user's collection
    public function addCardToCollection(Request $request)
    {
        // Validate the request
        $validated = $request->validate([
            'email' => 'required|email|exists:users,email',
            'card_id' => 'required|exists:cards,id',
            'count' => 'required|integer|min:1',
        ]);
    
        // Fetch user ID based on the email provided
        $user = DB::table('users')->where('email', $validated['email'])->first();
    
        // Log user information

    
        if (!$user) {
            return response()->json(['message' => 'User not found.'], 404);
        }
    
        // Create or update the card in the collection
        $collection = Collection::updateOrCreate(
            [
                'user_id' => $user->id,
                'card_id' => $validated['card_id'],
            ],
            ['count' => DB::raw("count + {$validated['count']}")]
        );
    
        // Log the collection result
      
    
        return response()->json($collection, 201);
    }
    
    
    // Fetch user's collection by email instead of user ID
    public function getUserCollection(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|exists:users,email', // Change to email instead of user_id
        ]);

        // Fetch user ID based on the email provided
        $user = DB::table('users')->where('email', $validated['email'])->first();

        if (!$user) {
            return response()->json(['message' => 'User not found.'], 404);
        }

        $collection = Collection::where('user_id', $user->id)->with('card')->get(); // Assuming a relationship exists
        return response()->json($collection);
    }

    // Remove card from user's collection using email
    public function removeCardFromCollection(Request $request, $cardId)
    {
        $validated = $request->validate([
            'email' => 'required|email|exists:users,email', // Change to email instead of user_id
        ]);

        // Fetch user ID based on the email provided
        $user = DB::table('users')->where('email', $validated['email'])->first();

        if (!$user) {
            return response()->json(['message' => 'User not found.'], 404);
        }

        $collection = Collection::where('user_id', $user->id)->where('card_id', $cardId)->first();

        if ($collection) {
            $collection->delete();
            return response()->json(['message' => 'Card removed from collection.'], 200);
        }

        return response()->json(['message' => 'Card not found in collection.'], 404);
    }
}
