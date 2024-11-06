<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Card;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class ImportImages extends Command
{
    protected $signature = 'import:images';
    protected $description = 'Download and store images for each card based on card_id';

    public function handle()
    {
        $cards = Card::whereNotNull('images')->get();

        foreach ($cards as $card) {
            $imageUrl = $card->images['large'] ?? null;

            if (!$imageUrl) {
                $this->warn("No large image URL found for card {$card->name}");
                continue;
            }

            try {
                // Disable SSL verification here, only for testing
                $imageContent = Http::withOptions(['verify' => false])->get($imageUrl)->body();

                // Define storage path
                $folderPath = 'card-img/' . $card->set->set_name; // store by set name
                $fileName = "{$card->card_id}_large.png";
                $filePath = "{$folderPath}/{$fileName}";

                // Save image to storage
                Storage::put($filePath, $imageContent);

                // Update the local_images column with the local path
                $card->update([
                    'local_images' => $filePath
                ]);

                $this->info("Image downloaded and saved for card {$card->name} at {$filePath}");

            } catch (\Exception $e) {
                $this->error("Failed to download image for card {$card->name}: {$e->getMessage()}");
            }
        }

        $this->info("All images have been processed.");
    }
}
