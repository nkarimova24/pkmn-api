<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Card;
use App\Models\Set;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schedule;

class CheckNewCards extends Command
{
    protected $signature = 'check:newcards';
    protected $description = 'Check for new Pokémon TCG cards and import them if they are not in the database';

    public function handle()
    {
        try {
            $sets = Set::all();

            foreach ($sets as $set) {
                $apiUrl = "https://api.pokemontcg.io/v2/cards?q=set.id:" . $set->ptcgo_code;
                $response = Http::withOptions(['verify' => false])->get($apiUrl);

                if ($response->failed()) {
                    $this->error("Failed to fetch cards for set: " . $set->set_name);
                    continue;
                }

                $cardsData = $response->json()['data'];
                
                foreach ($cardsData as $cardData) {
                    if (!Card::where('card_id', $cardData['id'])->exists()) {
                        $evolvesFrom = $this->getEvolvesFrom($cardData['id'], $cardsData);
                        
                        Card::create([
                            'card_id' => $cardData['id'],
                            'name' => $cardData['name'],
                            'supertype' => $cardData['supertype'],
                            'subtypes' => json_encode($cardData['subtypes'] ?? []),
                            'hp' => $cardData['hp'] ?? null,
                            'types' => json_encode($cardData['types'] ?? []),
                            'evolves_from' => $evolvesFrom,
                            'evolvesTo' => json_encode($cardData['evolvesTo'] ?? []),
                            'attacks' => json_encode($cardData['attacks'] ?? []),
                            'weakness' => json_encode($cardData['weaknesses'] ?? []),
                            'retreat_cost' => json_encode($cardData['retreatCost'] ?? []),
                            'converted_retreat_cost' => $cardData['convertedRetreatCost'] ?? null,
                            'rarity' => $cardData['rarity'] ?? null,
                            'legalities' => json_encode($cardData['legalities'] ?? []),
                            'images' => json_encode($cardData['images'] ?? []),
                            'image_small' => $cardData['images']['small'] ?? null,
                            'image_large' => $cardData['images']['large'] ?? null,
                            'set_id' => $set->id,
                        ]);
                        
                        $this->info("New card imported: " . $cardData['name']);
                    } else {
                        $this->info("Card already exists: " . $cardData['name']);
                    }
                }
            }
        } catch (\Exception $e) {
            $this->error('Error checking for new cards: ' . $e->getMessage());
        }
    }

    private function getEvolvesFrom($cardId, $cardsData)
    {
        foreach ($cardsData as $card) {
            if (isset($card['evolvesTo']) && in_array($cardId, $card['evolvesTo'])) {
                return $card['id'];
            }
        }
        return null;
    }

    public function schedule(Schedule $schedule): void
    {
        $schedule->command('check:newcards')->daily();
    }
}
