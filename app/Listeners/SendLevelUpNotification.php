<?php

namespace App\Listeners;

use App\Events\PlayerLeveledUp;
use App\Models\GameNotification;
use App\Notifications\LevelUpNotification;

class SendLevelUpNotification
{
    public function handle(PlayerLeveledUp $event): void
    {
        // Send email notification
        $event->player->notify(new LevelUpNotification($event->newLevel, $event->oldLevel));

        // Create in-game notification
        GameNotification::create([
            'player_id' => $event->player->id,
            'type' => 'level_up',
            'title' => 'Level Up!',
            'message' => 'You reached level ' . $event->newLevel . '!',
            'data' => [
                'new_level' => $event->newLevel,
                'old_level' => $event->oldLevel,
            ],
        ]);
    }
}
