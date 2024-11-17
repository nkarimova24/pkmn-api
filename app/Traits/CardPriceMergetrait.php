<?php 

namespace App\Traits;
use Illuminate\Support\facades\DB;
trait CardPriceMergetrait

// merge pricedata for single card

{  
    protected function getCardPrices(array $cardIds)  
    {
        $cardPrices = DB::table('cardprices')
            ->whereIn('id', $cardIds)
            ->select('id', 'tcgplayer', 'cardmarket')
            ->get()
            ->keyBy('id');

        //decoding pricedata since stored in array
        return $cardPrices->map(function ($priceData) {
            $decodedTcgplayerPrices = json_decode($priceData->tcgplayer, true);
            $decodedCardmarketPrices = json_decode($priceData->cardmarket, true);

            return [
                'id' => $priceData->id,
                'tcgplayer' => [
                    'url' => $decodedTcgplayerPrices['url'] ?? null,
                    'updatedAt' => $decodedTcgplayerPrices['updatedAt'] ?? null,
                    'normal' => $decodedTcgplayerPrices['prices']['normal'] ?? null,
                    'reverseHolofoil' => $decodedTcgplayerPrices['prices']['reverseHolofoil'] ?? null,
                    'holofoil' => $decodedTcgplayerPrices['prices']['holofoil'] ?? null,
                ],
                'cardmarket' => [
                    'url' => $decodedCardmarketPrices['url'] ?? null,
                    'updatedAt' => $decodedCardmarketPrices['updatedAt'] ?? null,
                    'prices' => [
                        'averageSellPrice' => $decodedCardmarketPrices['prices']['averageSellPrice'] ?? null,
                        'lowPrice' => $decodedCardmarketPrices['prices']['lowPrice'] ?? null,
                        'trendPrice' => $decodedCardmarketPrices['prices']['trendPrice'] ?? null,
                        'germanProLow' => $decodedCardmarketPrices['prices']['germanProLow'] ?? null,
                        'suggestedPrice' => $decodedCardmarketPrices['prices']['suggestedPrice'] ?? null,
                        'reverseHoloSell' => $decodedCardmarketPrices['prices']['reverseHoloSell'] ?? null,
                        'reverseHoloLow' => $decodedCardmarketPrices['prices']['reverseHoloLow'] ?? null,
                        'reverseHoloTrend' => $decodedCardmarketPrices['prices']['reverseHoloTrend'] ?? null,
                        'lowPriceExPlus' => $decodedCardmarketPrices['prices']['lowPriceExPlus'] ?? null,
                        'avg1' => $decodedCardmarketPrices['prices']['avg1'] ?? null,
                        'avg7' => $decodedCardmarketPrices['prices']['avg7'] ?? null,
                        'avg30' => $decodedCardmarketPrices['prices']['avg30'] ?? null,
                        'reverseHoloAvg1' => $decodedCardmarketPrices['prices']['reverseHoloAvg1'] ?? null,
                        'reverseHoloAvg7' => $decodedCardmarketPrices['prices']['reverseHoloAvg7'] ?? null,
                        'reverseHoloAvg30' => $decodedCardmarketPrices['prices']['reverseHoloAvg30'] ?? null,
                    ],
                ],
        
            ];
         });
     }
                    


    // price data extraction - not used
    private function extractPriceData($decodedTcgplayerPrices)
    {
        return [
            'url' => $decodedTcgplayerPrices['url'] ?? null,
            'updatedAt' => $decodedTcgplayerPrices['updatedAt'] ?? null,
            'normal' => ($decodedTcgplayerPrices['prices'] ?? null) ? ($decodedTcgplayerPrices['prices']['normal'] ?? null) : null,
            'reverseHolofoil' => ($decodedTcgplayerPrices['prices'] ?? null) ? ($decodedTcgplayerPrices['prices']['reverseHolofoil'] ?? null) : null,
            'holofoil' => ($decodedTcgplayerPrices['prices'] ?? null) ? ($decodedTcgplayerPrices['prices']['holofoil'] ?? null) : null,
        ];
    }

    //structure 
    private function extractCardmarketData($decodedCardmarketPrices) {
      return [
          'url' => $decodedCardmarketPrices['url'] ?? null,
          'updatedAt' => $decodedCardmarketPrices['updatedAt'] ?? null,
          'prices' => ($decodedCardmarketPrices['prices'] ?? null) ? $decodedCardmarketPrices['prices']  : null,

      ];

    }

}