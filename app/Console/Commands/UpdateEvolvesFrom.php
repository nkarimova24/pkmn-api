<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Card;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schedule;

class UpdateEvolvesFrom extends Command
{
    protected $signature = 'update:evolvesfrom';
    protected $description = 'Update evolves_from field in cards table and evolves_to table for existing Pokémon TCG cards';

    public function handle()
    {
        try {
            $cards = Card::all();
            $apiUrl = "https://api.pokemontcg.io/v2/cards";
            $response = Http::withOptions(['verify' => false])->get($apiUrl);

            if ($response->failed()) {
                $this->error('Failed to fetch cards data: ' . $response->status());
                return;
            }

            $cardsData = $response->json()['data'];
            
            foreach ($cards as $card) {
                $evolvesFrom = $this->getEvolvesFrom($card->card_id, $cardsData);
                if ($evolvesFrom !== $card->evolves_from) {
                    $card->update(['evolves_from' => $evolvesFrom]);
                    $this->info("Updated evolves_from for: " . $card->card_id . " <- " . ($evolvesFrom ?? 'None'));
                } else {
                    $this->info("No evolves_from update needed for: " . $card->card_id);
                }

                $evolvesTo = $this->getEvolvesTo($card->card_id, $cardsData);
                if (!empty($evolvesTo)) {
                    foreach ($evolvesTo as $evolvesToId) {
                        DB::table('evolves_to')->updateOrInsert(
                            [
                                'card_id' => $card->card_id,
                                'evolves_to_id' => $evolvesToId,
                            ]
                        );
                        $this->info("Updated evolves_to for: " . $card->card_id . " -> " . $evolvesToId);
                    }
                } else {
                    $this->info("No evolves_to update needed for: " . $card->card_id);
                }
            }
        } catch (\Exception $e) {
            $this->error('Error updating evolves_from and evolves_to: ' . $e->getMessage());
        }
    }

    private function getEvolvesFrom($cardId, $cardsData)
    {
        foreach ($cardsData as $card) {
            if (isset($card['evolvesTo']) && in_array($cardId, array_column($cardsData, 'name'))) {
                foreach ($cardsData as $searchCard) {
                    if ($searchCard['name'] === $cardId) {
                        return $searchCard['id'];
                    }
                }
            }
        }
        return null;
    }

    private function getEvolvesTo($cardId, $cardsData)
    {
        $evolvesToList = [];
        foreach ($cardsData as $card) {
            if (isset($card['evolvesFrom']) && $card['evolvesFrom'] === $cardId) {
                $evolvesToList[] = $card['id'];
            }
        }
        return $evolvesToList;
    }

    public function schedule(Schedule $schedule): void
    {
        $schedule->command('update:evolvesfrom')->daily();
    }
}