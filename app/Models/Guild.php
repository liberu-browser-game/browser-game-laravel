<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Guild extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
    ];

    public function members()
    {
        return $this->belongsToMany(Player::class, 'guild__memberships')
            ->withPivot('role', 'joined_at')
            ->withTimestamps();
    }

    public function memberships()
    {
        return $this->hasMany(Guild_Membership::class);
    }
}
