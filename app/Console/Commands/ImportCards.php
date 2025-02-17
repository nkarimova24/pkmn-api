<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Card;
use App\Models\Set;
use Illuminate\Support\Facades\File;
//importing all cards
class ImportCards extends Command
{
    
    protected $signature = 'import:cards';

    //    protected $description = 'Import cards';

    public function handle()
    {
        $setsJsonPath = base_path('database/data/sets.json');

        if (!File::exists($setsJsonPath)) {
            $this->error("Sets json file not found.");
            return;
        }

      
        $setsJson = File::get($setsJsonPath);
        $setsData = json_decode($setsJson, true);

 
        $setIdMap = [];

        foreach ($setsData as $setData) {
            $setIdMap[$setData['id']] = $setData['id']; //set_id
        }

      
        $cardsFolderPath = base_path('database/data/cards');

        //for each sets in the set sdata
        foreach ($setsData as $setData) {
            $setId = $setData['id'];
            $setFilePath = $cardsFolderPath . '/' . $setId . '.json'; 

         
            if (!File::exists($setFilePath)) {
                $this->warn("No cardfound for{$setData['name']} (ID: {$setId}).");
                continue; // Skip if no card file exists
            }

           //card json data from file 
            $cardJson = File::get($setFilePath);
            $cardsData = json_decode($cardJson, true);

            //find the corresponding set using setid
            $set = Set::where('set_name', $setData['name'])->first();

     
            if (!$set) {
                $this->error("Set {$setData['name']} not found");
                continue; 
            }

            foreach ($cardsData as $cardData) {
     
                Card::create([
                    'card_id' => $cardData['id'], 
                    'name' => $cardData['name'],
                    'supertype' => $cardData['supertype'],
                    'subtypes' => $cardData['subtypes'] ?? [],
                    'hp' => $cardData['hp'] ?? null,
                    'types' => $cardData['types'] ?? [],
                    'evolves_from' => $cardData['evolvesFrom'] ?? null,
                    'rules' => $cardData['rules'] ?? [],
                    'abilities' => $cardData['abilities'] ?? [],
                    'attacks' => $cardData['attacks'] ?? [],
                    'weakness' => $cardData['weaknesses'] ?? [],
                    'retreat_cost' => $cardData['retreatCost'] ?? [],
                    'converted_retreat_cost' => $cardData['convertedRetreatCost'] ?? null,
                    'rarity' => $cardData['rarity'] ?? null,
                    'legalities' => $cardData['legalities'] ?? [],
                    'images' => $cardData['images'] ?? [],
                    'set_id' => $set->id, 
                ]);
            }

            $this->info("Cards imported successfully for set: {$setData['name']}");
        }
    }
}
