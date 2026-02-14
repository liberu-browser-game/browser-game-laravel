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
        'rank',
        'score',
        'last_rank_update',
    ];


    protected $hidden = [
        'password',
        'remember_token',
    ];


    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_rank_update' => 'datetime',
    ];

    /**
     * Calculate score based on player performance metrics.
     */
    public function calculateScore(): int
    {
        $levelScore = $this->level * 100;
        $experienceScore = $this->experience;
        
        return $levelScore + $experienceScore;
    }
}