<?php

namespace App\Http\Controllers;

use App\Models\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CollectionController extends Controller
{
    //add card to users collection
    public function addCardToCollection(Request $request)
    {
    
        $validated = $request->validate([
            'email' => 'required|email|exists:users,email',
            'card_id' => 'required|exists:cards,id',
            'count' => 'required|integer|min:1',
        ]);
    
        //fetch user id based on email
        $user = DB::table('users')->where('email', $validated['email'])->first();
    
    
        if (!$user) {
            return response()->json(['message' => 'User not found.'], 404);
        }
    
        //create or update
        $collection = Collection::updateOrCreate(
            [
                'user_id' => $user->id,
                'card_id' => $validated['card_id'],
            ],
            ['count' => DB::raw("{$validated['count']}")]
        );

    
        return response()->json($collection, 201);
    }
    
    
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

    //remove card from user collection 
    public function removeCardFromCollection(Request $request, $cardId)
    {
        $validated = $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

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
