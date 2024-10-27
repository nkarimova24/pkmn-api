<?php

namespace App\Http\Controllers;

use App\Models\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
            'card_id' => 'required|exists:cards,id',
            'count' => 'required|integer|min:1',
        ]);

        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'User not found.'], 404);
        }

        // Create or update
        $collection = Collection::updateOrCreate(
            [
                'user_id' => $user->id,
                'card_id' => $validated['card_id'],
            ],
            ['count' => DB::raw("{$validated['count']}")]
        );

        return response()->json($collection, 201);
    }

    // Remove card from user's collection
    public function removeCardFromCollection(Request $request)
    {
        $validated = $request->validate([
            'card_id' => 'required|exists:cards,id',
            'count' => 'required|integer|min:1',
        ]);

        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'User not found.'], 404);
        }

        // Collection entry
        $collection = Collection::where([
            'user_id' => $user->id,
            'card_id' => $validated['card_id'],
        ])->first();

        if (!$collection) {
            return response()->json(['message' => 'Card not found in collection.'], 404);
        }

        // New count
        $newCount = $collection->count - $validated['count'];

        if ($newCount <= 0) {
            $collection->delete();
            return response()->json([
                'message' => 'Card removed from collection completely.',
                'count' => 0
            ], 200);
        } else {
            // Update with reduced / updated count
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