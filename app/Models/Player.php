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

    public function items()
    {
        return $this->hasMany(Player_Item::class);
    }

    public function quests()
    {
        return $this->hasMany(Player_Quest::class);
    }

    public function resources()
    {
        return $this->hasMany(Resource::class);
    }

    public function guild()
    {
        return $this->hasOneThrough(
            Guild::class,
            Guild_Membership::class,
            'player_id',
            'id',
            'id',
            'guild_id'
        );
    }
}