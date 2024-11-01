<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CardPrice extends Model
{
    // Specify the table if it's not the plural of the model name
    protected $table = 'cardprices';

    // Define the fillable properties
    protected $fillable = [
        'id', 
        'tcgplayer'
    ];

    
    protected $casts = [
        'tcgplayer' => 'array',
    ];
    public function cards()
    {
        return $this->hasMany(Card::class, 'cardprice_id', 'id');
    }
}
