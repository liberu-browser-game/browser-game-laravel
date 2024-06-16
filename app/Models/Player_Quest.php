<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Player_Quest extends Model
{
    use HasFactory;

    protected $fillable = [
        'player_id',
        'quest_id',
        'status',
    ];

    public function player()
    {
        return $this->belongsTo(Player::class);
    }

    public function quest()
    {
        return $this->belongsTo(Quest::class);
    }
}
