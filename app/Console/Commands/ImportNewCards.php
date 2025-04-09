<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Card;
use App\Models\Set;
use Illuminate\Support\Facades\Http;

class ImportNewCards extends Command
{
    protected $signature = 'import:new';
    protected $description = 'Import new cards and update missing fields';

    public function handle()
    {
        $apiUrl = "https://api.pokemontcg.io/v2/cards";
        $this->info("Fetching data from: " . $apiUrl);
        $response = Http::withOptions(['verify' => false])->get($apiUrl);

        if ($response->failed()) {
            $this->error("Failed to fetch cards from API.");
            return;
        }

        $cardsData = $response->json()['data'] ?? [];
        
        if (empty($cardsData)) {
            $this->warn("No cards found in API response.");
            return;
        }

        foreach ($cardsData as $cardData) {
            $existingCard = Card::where('card_id', $cardData['id'])->first();
            
            if ($existingCard) {
                // Update only missing fields
                $updateData = [];
                foreach ([
                    'name', 'supertype', 'subtypes', 'hp', 'types', 'attacks', 'weakness',
                    'retreat_cost', 'converted_retreat_cost', 'rarity', 'legalities', 'images', 'image_small', 'image_large'
                ] as $field) {
                    if (empty($existingCard->$field) && isset($cardData[$field])) {
                        $updateData[$field] = $cardData[$field];
                    }
                }

                // Handle evolves_to correctly
                if (empty($existingCard->evolves_to) && isset($cardData['evolvesTo'])) {
                    $evolvesToIds = [];
                    foreach ($cardData['evolvesTo'] as $evolvesToId) {
                        $nextEvolution = Card::where('card_id', $evolvesToId)->first();
                        if ($nextEvolution) {
                            $evolvesToIds[] = $nextEvolution->card_id;
                        }
                    }
                    if (!empty($evolvesToIds)) {
                        $updateData['evolves_to'] = json_encode($evolvesToIds);
                    }
                }

                if (!empty($updateData)) {
                    $existingCard->update($updateData);
                    $this->info("Updated missing fields for card: " . $cardData['name']);
                }
            } else {
                // Insert new card if it doesn't exist
                $set = Set::where('set_id', explode('-', $cardData['id'])[0])->first();
                Card::create([
                    'card_id' => $cardData['id'],
                    'name' => $cardData['name'],
                    'supertype' => $cardData['supertype'],
                    'subtypes' => $cardData['subtypes'] ?? [],
                    'hp' => $cardData['hp'] ?? null,
                    'types' => $cardData['types'] ?? [],
                    'evolves_to' => json_encode($cardData['evolvesTo'] ?? []),
                    'attacks' => $cardData['attacks'] ?? [],
                    'weakness' => $cardData['weaknesses'] ?? [],
                    'retreat_cost' => $cardData['retreatCost'] ?? [],
                    'converted_retreat_cost' => $cardData['convertedRetreatCost'] ?? null,
                    'rarity' => $cardData['rarity'] ?? null,
                    'legalities' => $cardData['legalities'] ?? [],
                    'images' => $cardData['images'] ?? [],
                    'image_small' => $cardData['images']['small'] ?? null,
                    'image_large' => $cardData['images']['large'] ?? null,
                    'set_id' => $set ? $set->id : null,
                ]);
                $this->info("Imported new card: " . $cardData['name']);
            }
        }
    }
}
