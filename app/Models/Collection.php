<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Collection extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'count',
        'normal_count',
        'holo_count',
        'reverse_holo_count',
        'card_id',

    ];

    /**
     * Get the user that owns the collection.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * Get the card that is associated with the collection.
     */
    public function card()
    {
        return $this->belongsTo(Card::class, 'card_id', 'card_id');
    }
}
