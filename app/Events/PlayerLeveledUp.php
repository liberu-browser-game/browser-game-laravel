<?php

namespace App\Events;

use App\Models\Player;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PlayerLeveledUp
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $player;
    public $newLevel;
    public $oldLevel;

    public function __construct(Player $player, $newLevel, $oldLevel)
    {
        $this->player = $player;
        $this->newLevel = $newLevel;
        $this->oldLevel = $oldLevel;
    }
}
