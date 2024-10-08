<?php

// app/Http/Controllers/PokemonController.php

namespace App\Http\Controllers;

use App\Models\Card; // Assuming you have a Pokemon model
use Illuminate\Http\Request;

class PokemonController extends Controller
{
    public function search(Request $request)
    {
       
        $query = $request->input('query');

        
        if (!$query) {
            return response()->json(['error' => 'Search query is required'], 400);
        }

        $results = Card::where('name', 'LIKE', '%' . $query . '%')->get();

        if ($results->isEmpty()) {
            return response()->json(['message' => 'No Pokémon found'], 404);
        }

        return response()->json($results);
    }
}
