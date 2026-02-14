<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'type',
        'rarity',
    ];

    public function players()
    {
        return $this->belongsToMany(Player::class, 'player__items')
            ->withPivot('quantity')
            ->withTimestamps();
    }

    public function questRewards()
    {
        return $this->hasMany(Quest::class, 'item_reward_id');
    }
}