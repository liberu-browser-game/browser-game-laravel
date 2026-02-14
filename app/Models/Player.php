<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Player extends Model
{
    use HasFactory;

    protected $fillable = [
        'username',
        'email',
        'password',
        'level',
        'experience',
    ];


    protected $hidden = [
        'password',
        'remember_token',
    ];


    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Get the player's inventory items.
     */
    public function playerItems()
    {
        return $this->hasMany(Player_Item::class);
    }

    /**
     * Get the items in the player's inventory.
     */
    public function items()
    {
        return $this->belongsToMany(Item::class, 'player__items')
            ->withPivot('quantity')
            ->withTimestamps();
    }
}