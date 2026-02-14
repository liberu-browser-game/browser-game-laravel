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

    public function profile()
    {
        return $this->hasOne(Player_Profile::class);
    }

    public function quests()
    {
        return $this->hasMany(Player_Quest::class);
    }

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
    }
}