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
    public function quests()
    {
        return $this->belongsToMany(Quest::class, 'player__quests')
            ->withPivot('status')
            ->withTimestamps();
    }

    public function activeQuests()
    {
        return $this->quests()->wherePivot('status', 'in-progress');
    }

    public function completedQuests()
    {
        return $this->quests()->wherePivot('status', 'completed');
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
    public function profile()
    {
        return $this->hasOne(Player_Profile::class);
    }

    public function quests()
    {
        return $this->hasMany(Player_Quest::class);
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
    public function items()
    {
        return $this->hasMany(Player_Item::class);
    }

    public function statistics()
    {
        return $this->hasOne(PlayerStatistic::class);
    }

    public function achievements()
    {
        return $this->belongsToMany(Achievement::class, 'player_achievements')
            ->withPivot('unlocked_at', 'progress')
            ->withTimestamps();
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