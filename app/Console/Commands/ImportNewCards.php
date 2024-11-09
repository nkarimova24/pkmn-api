<?php
//importing new cards if comes out! 
//update ptcg_code accordingly!
//update apiUrl acoordingly!

//before importing cards, make sure imported new set first!
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Card;
use App\Models\Set;
// use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;

class ImportNewCards extends Command
{
    protected $signature = 'import:new';

    public function handle()
    {
      //corresponding set by ptcg_code
        $set = Set::where('ptcgo_code', 'SSP')->first();

        if (!$set) {
            $this->error("Set sv8 not found in the database.");
            return;
        }

        //fetch cards by set url
        $apiUrl = "https://api.pokemontcg.io/v2/cards?q=set.id:sv8";

        $response = Http::withOptions(['verify' => false])->get($apiUrl);

        if ($response->failed()) {
            $this->error("Failed to fetch cards for set sv8");
            return;
        }

        $cardsData = $response->json()['data'];
        
        foreach ($cardsData as $cardData) {
            $evolvesFrom = $this->getEvolvesFrom($cardData['id'], $cardsData);
            
            Card::create([
                'card_id' => $cardData['id'],
                'name' => $cardData['name'],
                'supertype' => $cardData['supertype'],
                'subtypes' => $cardData['subtypes'] ?? [],
                'hp' => $cardData['hp'] ?? null,
                'types' => $cardData['types'] ?? [],
                'evolves_from' => $evolvesFrom,
                'evolvesTo' => $cardData['evolvesTo'] ?? [],
                'attacks' => $cardData['attacks'] ?? [],
                'weakness' => $cardData['weaknesses'] ?? [],
                'retreat_cost' => $cardData['retreatCost'] ?? [],
                'converted_retreat_cost' => $cardData['convertedRetreatCost'] ?? null,
                'rarity' => $cardData['rarity'] ?? null,
                'legalities' => $cardData['legalities'] ?? [],
                'images' => $cardData['images'] ?? [],
                'image_small' => $cardData['images']['small'] ?? null,
                'image_large' => $cardData['images']['large'] ?? null,
                'set_id' => $set->id,
            ]);
        }

        $this->info("Cards from sv8 set imported successfully.");
    }

    /**
     * function to calculate the evolves_from value based on the evolvesTo field.
     * @param string $cardId
     * @param array $cardsData
     * @return string|null
     */
    private function getEvolvesFrom($cardId, $cardsData)
    {
        foreach ($cardsData as $card) {
            if (isset($card['evolvesTo']) && in_array($cardId, $card['evolvesTo'])) {
                return $card['id'];
            }
        }
        return null;
    }
}