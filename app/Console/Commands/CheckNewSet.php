<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Series;
use App\Models\Set;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schedule;

class CheckNewSet extends Command
{
    protected $signature = 'check:newsets';
    protected $description = 'Check for new Pokémon TCG sets and import them if they are not in the database';

    public function handle()
    {
        try {
            $apiUrl = "https://api.pokemontcg.io/v2/sets";
            $response = Http::withOptions(['verify' => false])->get($apiUrl);

            if ($response->failed()) {
                $this->error('Failed to fetch sets: ' . $response->status());
                return;
            }

            $sets = $response->json()['data'];

            foreach ($sets as $setData) {
                if (!Set::where('set_name', $setData['name'])->exists()) {
                    $series = Series::firstOrCreate([
                        'series_name' => $setData['series']
                    ]);

                    Set::create([
                        'set_name' => $setData['name'],
                        'ptcgo_code' => $setData['ptcgoCode'] ?? null,
                        'release_date' => $setData['releaseDate'],
                        'printed_total' => $setData['printedTotal'] ?? null,
                        'total' => $setData['total'] ?? null,
                        'legalities' => json_encode($setData['legalities'] ?? []),
                        'images' => json_encode($setData['images'] ?? []),
                        'series_id' => $series->id,
                    ]);

                    $this->info('New set imported: ' . $setData['name']);
                } else {
                    $this->info('Set already exists: ' . $setData['name']);
                }
            }
        } catch (\Exception $e) {
            $this->error('Error checking for new sets: ' . $e->getMessage());
        }
    }

    public function schedule(Schedule $schedule): void
    {
        $schedule->command('check:newsets')->daily();
    }
}