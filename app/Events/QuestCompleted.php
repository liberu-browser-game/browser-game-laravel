<?php

namespace App\Events;

use App\Models\Player;
use App\Models\Quest;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class QuestCompleted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $player;
    public $quest;
    public $reward;

    public function __construct(Player $player, Quest $quest, $reward = null)
    {
        $this->player = $player;
        $this->quest = $quest;
        $this->reward = $reward;
    }
}
