<?php

namespace App\Listeners;

use App\Events\QuestCompleted;
use App\Models\GameNotification;
use App\Notifications\QuestCompletedNotification;

class SendQuestCompletedNotification
{
    public function handle(QuestCompleted $event): void
    {
        // Send email notification
        $event->player->notify(new QuestCompletedNotification($event->quest, $event->reward));

        // Create in-game notification
        GameNotification::create([
            'player_id' => $event->player->id,
            'type' => 'quest_completed',
            'title' => 'Quest Completed!',
            'message' => 'You have successfully completed: ' . $event->quest->name,
            'data' => [
                'quest_id' => $event->quest->id,
                'quest_name' => $event->quest->name,
                'reward' => $event->reward,
            ],
        ]);
    }
}
