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
}