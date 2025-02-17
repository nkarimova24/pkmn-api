<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Set extends Model
{
    use HasFactory;

    protected $fillable = ['set_name',  
                           'ptcgo_code',
                           'release_date',
                           'printed_total',
                           'total',
                           'legalities',
                           'images',
                           'series_id'];

    public function series() {
        return $this->belongsTo(Series::class, 'series_id', 'id');
    }
    public function cards()
    {
        return $this->hasMany(Card::class, 'set_id');
    }
    protected $casts = [
        'legalities' => 'array',
        'images' => 'array',
    ];

  

}
