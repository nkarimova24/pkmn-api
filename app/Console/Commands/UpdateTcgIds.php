<?php
//unknown what this does
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Card;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class UpdateTcgIds extends Command
{
    protected $signature = 'update:tcg-ids';
    protected $description = 'Update TCG IDs for cards';

    public function handle()
    {
        $cards = Card::whereNull('tcg_id')->get();
        $config = require base_path('configure.js');
        $tcgApiHost = $config['host'];
        $tcgApiKey = $config['apiKey'];

        foreach ($cards as $card) {
            $response = Http::withHeaders([
                'X-Api-Key' => $tcgApiKey,
            ])->get($tcgApiHost . '/cards', ['q' => 'name:"' . $card->name . '"']);


            if ($response->successful() && isset($response->json()['data'][0]['id'])) { //check if exists first
                $tcgId = $response->json()['data'][0]['id'];
                $card->tcg_id = $tcgId;
                $card->save();
                Log::info("Updated TCG ID for card: " . $card->name);
            } else {
                Log::error("Could not find TCG ID for card: " . $card->name . ' Response: ' . $response->body() ); // Add more info for debugging
            }

        }

        $this->info('TCG IDs updated.');  // Use $this->info for command output
    }
}