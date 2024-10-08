<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Series;
use App\Models\Set;
use Illuminate\Support\Facades\File; 

class ImportSets extends Command
{
        protected $signature = 'import:sets';

   
    protected $description = 'Importing sets and series from sets.json';

    
    public function handle()
    {
      
        $json = File::get(base_path('database\data\sets.json')); 
        $sets = json_decode($json, true);

        foreach ($sets as $setData) {
            
            $series = Series::firstOrCreate(['series_name' => $setData['series']]);

           
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
        }

        $this->info('Sets imported :)');
    }
}

