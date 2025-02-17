<?php


//importing new set if comes out! 
//update setid accordingly!
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Series;
use App\Models\Set;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
class ImportNewSets extends Command
{
        protected $signature = 'import:newsets';

   
    protected $description = 'Importing sets and series from sets.json';

    
    public function handle()
    {
        try {
            $apiUrl = "https://api.pokemontcg.io/v2/sets/sv8";
            $response = Http::withOptions(['verify' => false])->get($apiUrl);

            if ($response->failed()) {
                $this->error('Failed to fetch set sv8: ' . $response->status());
                return;
            }

            $setData = $response->json()['data'];
            
            //create or get series
            $series = Series::firstOrCreate([
                'series_name' => $setData['series']
            ]);

            Set::create([
                'set_name' => $setData['name'],
                'ptcgo_code' => $setData['ptcgoCode'] ?? null,
                'release_date' => $setData['releaseDate'],
                'printed_total' => $setData['printedTotal'] ?? null,
                'total' => $setData['total'] ?? null,
                'legalities' => $setData['legalities'] ?? [],
                'images' => $setData['images'] ?? [],
                'series_id' => $series->id,
            ]);

            $this->info('Set sv8 (Paradox Rift) imported successfully!');

        } catch (\Exception $e) {
            $this->error('Error importing set: ' . $e->getMessage());
        }
    }
}