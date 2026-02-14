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

    // Relationships
    public function profile()
    {
        return $this->hasOne(Player_Profile::class);
    }

    public function items()
    {
        return $this->belongsToMany(Item::class, 'player__items')
            ->withPivot('quantity')
            ->withTimestamps();
    }

    public function resources()
    {
        return $this->hasMany(Resource::class);
    }

    public function quests()
    {
        return $this->belongsToMany(Quest::class, 'player__quests')
            ->withPivot('status')
            ->withTimestamps();
    }

    public function guildMemberships()
    {
        return $this->hasMany(Guild_Membership::class);
    }

    public function guilds()
    {
        return $this->belongsToMany(Guild::class, 'guild__memberships')
            ->withPivot('role', 'joined_at')
            ->withTimestamps();
    }
}