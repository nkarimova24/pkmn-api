<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Card extends Model
{
    use HasFactory;

    protected $fillable = [
        'card_id', 'name', 'supertype', 'subtypes', 'hp', 'types', 'evolves_from',
        'rules','attacks','weakness','retreat_cost','converted_retreat_cost','rarity','legalities',
        'images', 'set_id'


    ];

    protected $casts = [ 
        'subtypes' => 'array',
        'types'=> 'array',
        'rules'=> 'array',  
        'abilities'=> 'array',
        'attacks' => 'array',
        'weakness'=> 'array',
        'retreat_cost'=> 'array',
        'legalities'=> 'array',
        'images'=> 'array'
    ];

    public function set(){
        return $this->belongsTo(Set::class);
    }

    public function collections()
{
    return $this->hasMany(Collection::class);
}

}
